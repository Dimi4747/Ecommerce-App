#!/usr/bin/env node

/**
 * Deploy Assets Script
 * 
 * Copies compiled frontend assets from frontend/dist/ to backend/public/build/
 * This script uses a copy strategy instead of symlinks for Windows compatibility.
 * 
 * Usage:
 *   node scripts/deploy-assets.js
 *   npm run deploy:assets
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Paths
const frontendDistPath = path.resolve(__dirname, '../dist');
const backendPublicBuildPath = path.resolve(__dirname, '../../backend/public/build');
const manifestSourcePath = path.join(frontendDistPath, '.vite/manifest.json');
const manifestDestPath = path.join(backendPublicBuildPath, 'manifest.json');

/**
 * Recursively copy directory contents
 */
function copyDirectory(source, destination) {
    // Create destination directory if it doesn't exist
    if (!fs.existsSync(destination)) {
        fs.mkdirSync(destination, { recursive: true });
    }

    // Read source directory
    const entries = fs.readdirSync(source, { withFileTypes: true });

    for (const entry of entries) {
        const sourcePath = path.join(source, entry.name);
        const destPath = path.join(destination, entry.name);

        if (entry.isDirectory()) {
            // Skip .vite directory (we'll handle manifest separately)
            if (entry.name === '.vite') {
                continue;
            }
            // Recursively copy subdirectory
            copyDirectory(sourcePath, destPath);
        } else {
            // Copy file
            fs.copyFileSync(sourcePath, destPath);
        }
    }
}

/**
 * Clean the build directory
 */
function cleanBuildDirectory() {
    if (fs.existsSync(backendPublicBuildPath)) {
        console.log('🧹 Cleaning existing build directory...');
        fs.rmSync(backendPublicBuildPath, { recursive: true, force: true });
    }
}

/**
 * Main deployment function
 */
function deployAssets() {
    console.log('📦 Deploying frontend assets to backend...\n');

    // Validate source directory exists
    if (!fs.existsSync(frontendDistPath)) {
        console.error('❌ Error: frontend/dist/ directory not found.');
        console.error('   Please run "npm run build" first to compile the frontend.');
        process.exit(1);
    }

    // Validate manifest exists
    if (!fs.existsSync(manifestSourcePath)) {
        console.error('❌ Error: manifest.json not found in frontend/dist/.vite/');
        console.error('   Please ensure the build completed successfully.');
        process.exit(1);
    }

    try {
        // Clean existing build directory
        cleanBuildDirectory();

        // Create build directory
        console.log('📁 Creating backend/public/build/ directory...');
        fs.mkdirSync(backendPublicBuildPath, { recursive: true });

        // Copy all assets from dist/ to public/build/
        console.log('📋 Copying assets...');
        copyDirectory(frontendDistPath, backendPublicBuildPath);

        // Copy manifest.json to the root of build directory
        console.log('📄 Copying manifest.json...');
        fs.copyFileSync(manifestSourcePath, manifestDestPath);

        // Verify deployment
        const assetsDir = path.join(backendPublicBuildPath, 'assets');
        if (!fs.existsSync(assetsDir)) {
            throw new Error('Assets directory not found after copy');
        }

        const assetFiles = fs.readdirSync(assetsDir);
        const manifestContent = JSON.parse(fs.readFileSync(manifestDestPath, 'utf-8'));

        console.log('\n✅ Deployment successful!\n');
        console.log(`   Assets copied: ${assetFiles.length} files`);
        console.log(`   Manifest entries: ${Object.keys(manifestContent).length}`);
        console.log(`   Destination: ${backendPublicBuildPath}`);
        console.log('\n📍 Assets are now available at: backend/public/build/');
        console.log('   The backend can now serve these assets in production mode.\n');

    } catch (error) {
        console.error('\n❌ Deployment failed:', error.message);
        process.exit(1);
    }
}

// Run deployment
deployAssets();
