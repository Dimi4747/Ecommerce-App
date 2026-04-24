# Asset Deployment Strategy

## Overview

This document describes the asset serving strategy for the separated backend-frontend architecture.

## Decision: Copy Strategy

**Strategy Selected:** Copy compiled assets from `frontend/dist/` to `backend/public/build/`

### Rationale

1. **Windows Compatibility**: Symlinks require administrator privileges on Windows and may not work reliably across different Windows versions
2. **Cross-Platform Consistency**: Copy strategy works identically on Windows, macOS, and Linux
3. **CI/CD Simplicity**: No special permissions or symlink support needed in deployment pipelines
4. **Explicit Deployment**: Clear separation between build and deployment steps
5. **No Runtime Dependencies**: Backend doesn't need access to frontend source directory

### Alternative Considered: Symlink Strategy

**Why Not Symlinks?**
- Requires elevated privileges on Windows
- May break in certain deployment environments
- Can cause issues with file watchers and IDEs
- Less transparent deployment process

## Implementation

### Deployment Script

**Location:** `frontend/scripts/deploy-assets.js`

**Functionality:**
1. Validates that `frontend/dist/` exists
2. Validates that `manifest.json` exists
3. Cleans existing `backend/public/build/` directory
4. Recursively copies all assets from `dist/` to `public/build/`
5. Copies `manifest.json` to root of `public/build/`
6. Verifies deployment success

**Usage:**
```bash
cd frontend
npm run deploy:assets
```

### Directory Structure

**Before Deployment:**
```
frontend/
└── dist/
    ├── .vite/
    │   └── manifest.json
    ├── assets/
    │   ├── app-[hash].js
    │   ├── app-[hash].css
    │   └── [other-chunks].js
    └── hot
```

**After Deployment:**
```
backend/
└── public/
    └── build/
        ├── manifest.json          (copied from dist/.vite/manifest.json)
        ├── assets/
        │   ├── app-[hash].js
        │   ├── app-[hash].css
        │   └── [other-chunks].js
        └── hot
```

### Manifest Accessibility

The `manifest.json` file is copied from `frontend/dist/.vite/manifest.json` to `backend/public/build/manifest.json` to ensure Laravel can read it at the configured path.

**Laravel Configuration:**
```php
// backend/config/vite.php
'manifest_path' => env('VITE_MANIFEST_PATH', public_path('build/manifest.json')),
```

## Production Workflow

### 1. Build Frontend
```bash
cd frontend
npm run build
```

**Output:** Compiled assets in `frontend/dist/`

### 2. Deploy Assets
```bash
npm run deploy:assets
```

**Output:** Assets copied to `backend/public/build/`

### 3. Verify Deployment
- Check `backend/public/build/manifest.json` exists
- Check `backend/public/build/assets/` contains JS and CSS files
- Verify manifest contains entry for `src/app.jsx`

### 4. Deploy Backend
- Backend serves assets from `public/build/`
- Laravel reads manifest to resolve asset paths with cache-busting hashes

## Development Workflow

In development mode, **no asset deployment is needed**. Assets are served directly from the Vite dev server:

1. Start backend: `cd backend && php artisan serve`
2. Start frontend: `cd frontend && npm run dev`
3. Backend loads assets from `http://localhost:5173` with HMR

## Configuration

### Backend Configuration

**File:** `backend/config/vite.php`

```php
return [
    'build_path' => env('VITE_BUILD_PATH', 'build'),
    'manifest_path' => env('VITE_MANIFEST_PATH', public_path('build/manifest.json')),
    'dev_server_url' => env('VITE_DEV_SERVER_URL', 'http://localhost:5173'),
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173'),
];
```

**Environment Variables:**
```env
VITE_BUILD_PATH=build
VITE_MANIFEST_PATH=public/build/manifest.json
```

### Frontend Configuration

**File:** `frontend/vite.config.js`

```javascript
export default defineConfig(({ mode }) => ({
    base: mode === 'production' 
        ? (process.env.VITE_ASSET_URL || '/build/')
        : '/',
    
    build: {
        outDir: 'dist',
        manifest: true,
        rollupOptions: {
            input: 'src/app.jsx',
        },
    },
}));
```

**Environment Variables:**
```env
VITE_ASSET_URL=/build/
```

## Git Configuration

The `backend/public/build/` directory is excluded from version control:

**File:** `backend/.gitignore`
```
/public/build
```

**Rationale:**
- Compiled assets are build artifacts, not source code
- Assets should be built during deployment, not committed
- Reduces repository size
- Prevents merge conflicts on generated files

## CI/CD Integration

### Example GitHub Actions Workflow

```yaml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      # Build frontend
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'
      
      - name: Install frontend dependencies
        run: cd frontend && npm ci
      
      - name: Build frontend
        run: cd frontend && npm run build
      
      - name: Deploy assets to backend
        run: cd frontend && npm run deploy:assets
      
      # Deploy backend
      - name: Deploy to server
        run: |
          # Your deployment commands here
          # The backend/public/build/ directory now contains all assets
```

## Troubleshooting

### Issue: "frontend/dist/ directory not found"

**Cause:** Frontend hasn't been built yet

**Solution:**
```bash
cd frontend
npm run build
npm run deploy:assets
```

### Issue: "manifest.json not found"

**Cause:** Build failed or didn't complete

**Solution:**
1. Check build output for errors
2. Verify `frontend/dist/.vite/manifest.json` exists
3. Rebuild: `npm run build`

### Issue: Assets not loading in production

**Symptoms:** 404 errors for `/build/assets/*.js` files

**Checklist:**
- [ ] `backend/public/build/manifest.json` exists
- [ ] `backend/public/build/assets/` contains JS/CSS files
- [ ] `APP_ENV=production` in backend `.env`
- [ ] `base` URL in `vite.config.js` is `/build/`
- [ ] Laravel can read the manifest file

**Solution:**
```bash
# Rebuild and redeploy
cd frontend
npm run build
npm run deploy:assets

# Verify files exist
ls -la ../backend/public/build/
```

### Issue: Stale assets after rebuild

**Cause:** Old assets not cleaned before new deployment

**Solution:**
The deployment script automatically cleans the build directory. If issues persist:
```bash
# Manual cleanup
rm -rf backend/public/build/
cd frontend
npm run deploy:assets
```

## Requirements Satisfied

This implementation satisfies the following requirements from the requirements document:

- **Requirement 4.3**: Backend exposes route/directory for serving compiled assets
  - ✅ Assets served from `backend/public/build/`
  
- **Requirement 4.4**: Asset manifest accessible to backend via configurable path
  - ✅ Manifest copied to `backend/public/build/manifest.json`
  - ✅ Path configured in `backend/config/vite.php`

## Future Considerations

### CDN Deployment

For production at scale, consider deploying assets to a CDN:

1. Build frontend: `npm run build`
2. Upload `frontend/dist/` to CDN (e.g., AWS S3 + CloudFront)
3. Update `VITE_ASSET_URL` to CDN URL
4. Backend serves HTML, CDN serves assets

**Benefits:**
- Reduced backend server load
- Faster asset delivery via CDN edge locations
- Better caching and compression

### Automated Deployment

Consider automating the deployment in the build script:

```json
{
  "scripts": {
    "build": "vite build",
    "build:production": "vite build && node scripts/deploy-assets.js"
  }
}
```

This ensures assets are always deployed after a successful build.
