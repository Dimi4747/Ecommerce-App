# Frontend Build Scripts

This directory contains scripts for building and deploying the frontend application.

## Asset Deployment Strategy

### Copy Strategy (Recommended)

This project uses a **copy strategy** for deploying compiled frontend assets to the backend. The compiled assets from `frontend/dist/` are copied to `backend/public/build/` for production serving.

**Why Copy Instead of Symlink?**

1. **Windows Compatibility**: Symlinks require administrator privileges on Windows and may not work reliably across different Windows versions
2. **Deployment Simplicity**: Copy strategy works consistently across all operating systems (Windows, macOS, Linux)
3. **CI/CD Friendly**: No special permissions or symlink support needed in deployment pipelines
4. **Clear Separation**: Explicit copy step makes the deployment process more transparent

### Scripts

#### `deploy-assets.js`

Copies compiled frontend assets from `frontend/dist/` to `backend/public/build/`.

**Usage:**
```bash
npm run deploy:assets
```

**What it does:**
1. Validates that `frontend/dist/` exists (ensures build was run)
2. Validates that `manifest.json` exists
3. Cleans the existing `backend/public/build/` directory
4. Copies all assets from `dist/` to `public/build/`
5. Copies `manifest.json` to the root of `public/build/`
6. Verifies the deployment was successful

**Requirements:**
- Frontend must be built first: `npm run build`
- Backend directory must exist at `../backend/`

**Output:**
- Assets are copied to: `backend/public/build/assets/`
- Manifest is copied to: `backend/public/build/manifest.json`

### Production Deployment Workflow

1. **Build the frontend:**
   ```bash
   cd frontend
   npm run build
   ```

2. **Deploy assets to backend:**
   ```bash
   npm run deploy:assets
   ```

3. **Verify deployment:**
   - Check that `backend/public/build/manifest.json` exists
   - Check that `backend/public/build/assets/` contains compiled JS and CSS files

4. **Deploy backend:**
   - The backend can now serve the compiled assets in production mode
   - Laravel will read `manifest.json` to resolve asset paths with cache-busting hashes

### Development Workflow

In development mode, assets are served directly from the Vite dev server (no deployment needed):

1. **Start backend server:**
   ```bash
   cd backend
   php artisan serve
   ```

2. **Start frontend dev server:**
   ```bash
   cd frontend
   npm run dev
   ```

The backend will automatically load assets from the Vite dev server at `http://localhost:5173` with Hot Module Replacement (HMR) enabled.

### Configuration

The asset serving configuration is defined in:

- **Backend:** `backend/config/vite.php`
  - `build_path`: Directory where compiled assets are stored (default: `build`)
  - `manifest_path`: Path to manifest.json (default: `public/build/manifest.json`)
  - `dev_server_url`: Vite dev server URL (default: `http://localhost:5173`)

- **Frontend:** `frontend/vite.config.js`
  - `build.outDir`: Output directory for compiled assets (default: `dist`)
  - `build.manifest`: Enable manifest generation (default: `true`)
  - `base`: Base URL for assets in production (default: `/build/`)

### Troubleshooting

**Error: "frontend/dist/ directory not found"**
- Solution: Run `npm run build` first to compile the frontend

**Error: "manifest.json not found"**
- Solution: Ensure the build completed successfully without errors

**Assets not loading in production**
- Check that `backend/public/build/manifest.json` exists
- Verify that `APP_ENV=production` in backend `.env`
- Check browser console for 404 errors on asset paths
- Verify that `base` URL in `vite.config.js` matches Laravel's public path

**Stale assets after rebuild**
- The deployment script automatically cleans the build directory before copying
- If issues persist, manually delete `backend/public/build/` and run deployment again
