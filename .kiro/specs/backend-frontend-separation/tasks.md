# Implementation Plan: Backend-Frontend Separation

## Overview

This implementation plan transforms a monolithic Laravel + Inertia.js + React application into two independent projects: a Backend API (Laravel) and a Frontend Application (React). The approach follows a phased migration strategy to minimize disruption while ensuring all functionality remains intact.

**Key Implementation Strategy:**
- Restructure frontend from Laravel convention (`resources/js/`) to standalone React structure (`src/`)
- Keep `laravel-vite-plugin` for Laravel compatibility while achieving physical separation
- Configure plugin to work with separated structure via `input` and `buildDirectory` options
- Setup CORS and session management for cross-origin communication
- Maintain Laravel Breeze authentication functionality throughout migration
- Leverage plugin's automatic dev/production mode detection via `hot` file

## Tasks

- [x] 1. Restructure Frontend Project to Standalone React Structure
  - Move all React files from `backend/resources/js/` to `frontend/src/`
  - Move CSS files from `backend/resources/css/` to `frontend/src/`
  - Update import paths in all React components to use new `src/` structure
  - Create `frontend/src/app.jsx` as the new entry point
  - _Requirements: 1.1, 1.2, 8.2, 8.4_

- [x] 2. Configure Vite for Separated Structure (Keep Laravel Plugin)
  - [x] 2.1 Update `frontend/vite.config.js` to work with separated structure
    - Keep `laravel-vite-plugin` import and configuration
    - Configure `input: ['src/app.jsx']` to point to new frontend location
    - Set `buildDirectory: 'dist'` for output location
    - Configure `base` URL for asset paths (production vs development)
    - Enable CORS for dev server
    - Add path alias configuration (`@` → `./src`)
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

  - [x] 2.2 Verify `frontend/package.json` dependencies
    - Keep `laravel-vite-plugin` in devDependencies (required for Laravel compatibility)
    - Verify `@inertiajs/react`, `react`, `react-dom`, and `vite` versions
    - Add build and dev scripts if not present
    - _Requirements: 2.1, 7.2, 7.4_

  - [x] 2.3 Update Inertia app initialization in `frontend/src/app.jsx`
    - Replace `resolvePageComponent` helper with direct `import.meta.glob`
    - Remove dependency on `laravel-vite-plugin/inertia-helpers`
    - Configure dynamic page component loading with proper path resolution
    - _Requirements: 3.1, 3.2_

- [x] 3. Configure Backend to Work with Separated Frontend
  - [x] 3.1 Update `backend/config/vite.php` configuration file
    - Define `build_directory` pointing to `../frontend/dist`
    - Define `hot_file` path for dev server detection
    - Plugin handles manifest reading automatically
    - _Requirements: 4.4, 6.3, 6.4_

  - [x] 3.2 Update `backend/resources/views/app.blade.php` template
    - Simplify to use `@vite(['../frontend/src/app.jsx'])` directive
    - Plugin automatically detects dev vs production mode via `hot` file
    - Remove conditional logic (plugin handles environment detection)
    - Ensure `@inertia` and `@inertiaHead` directives are present
    - _Requirements: 3.3, 3.4, 4.1, 4.2_

  - [x] 3.3 Verify Laravel can access frontend build directory
    - Ensure `../frontend/dist` path is accessible from backend
    - Plugin reads manifest from this location automatically
    - No symlink or copy strategy needed (plugin handles paths)
    - _Requirements: 4.3, 4.4_

- [ ] 4. Configure CORS for Cross-Origin Communication
  - [x] 4.1 Update `backend/config/cors.php` configuration
    - Add frontend URL to `allowed_origins` (from env variable)
    - Configure `paths` to include Inertia routes and auth endpoints
    - Set `supports_credentials` to true for session cookies
    - Configure `allowed_methods` and `allowed_headers`
    - _Requirements: 5.1, 5.4_

  - [ ] 4.2 Verify CORS middleware is registered in HTTP kernel
    - Check that `HandleCors` middleware is in global middleware stack
    - Ensure middleware runs before other middleware that might block requests
    - _Requirements: 5.1_

- [ ] 5. Configure Session Management for Cross-Domain Authentication
  - [ ] 5.1 Update `backend/config/session.php` for cross-domain cookies
    - Configure `SESSION_DOMAIN` environment variable support
    - Set `same_site` attribute appropriately (lax for development)
    - Configure `secure` cookie attribute based on environment
    - Ensure `http_only` is true for security
    - _Requirements: 5.2, 5.3, 10.3_

  - [ ] 5.2 Update `backend/config/sanctum.php` for stateful domains
    - Configure `SANCTUM_STATEFUL_DOMAINS` to include frontend URL
    - Support both localhost and production domains
    - Parse comma-separated domain list from environment variable
    - _Requirements: 5.3, 10.4_

- [ ] 6. Setup Environment Variables for Both Projects
  - [ ] 6.1 Configure backend environment variables in `backend/.env`
    - Add `FRONTEND_URL` for CORS and Sanctum configuration
    - Add `VITE_BUILD_DIRECTORY` pointing to `../frontend/dist`
    - Add `SESSION_DOMAIN`, `SESSION_SAME_SITE`, `SESSION_SECURE_COOKIE`
    - Add `SANCTUM_STATEFUL_DOMAINS` with frontend domains
    - Plugin handles dev server detection automatically (no manual URL needed)
    - _Requirements: 6.1, 6.3, 5.2, 5.3_

  - [ ] 6.2 Create `frontend/.env` file with frontend environment variables
    - Add `VITE_API_URL` pointing to backend URL
    - Add `VITE_APP_NAME` for application title
    - No `VITE_ASSET_URL` needed (plugin handles asset paths)
    - _Requirements: 6.2, 6.4_

  - [ ] 6.3 Create `backend/.env.example` with all new variables documented
    - Document purpose of each new environment variable
    - Provide example values for development and production
    - Note that plugin handles most configuration automatically
    - _Requirements: 6.1, 6.3, 9.3_

  - [ ] 6.4 Create `frontend/.env.example` with frontend variables documented
    - Document purpose of each frontend environment variable
    - Provide example values for development and production
    - _Requirements: 6.2, 6.4, 9.4_

- [ ] 7. Create API Client Configuration in Frontend
  - Create `frontend/src/bootstrap.js` for Axios configuration
  - Configure `axios.defaults.baseURL` from environment variable
  - Set `axios.defaults.withCredentials = true` for session cookies
  - Configure default headers (`X-Requested-With`, `Accept`)
  - Import bootstrap.js in `frontend/src/app.jsx`
  - _Requirements: 5.4, 10.3_

- [ ] 8. Update Inertia Middleware to Share Authentication State
  - Verify `backend/app/Http/Middleware/HandleInertiaRequests.php` shares auth.user
  - Ensure flash messages and errors are shared with frontend
  - Configure `$rootView` property to point to correct blade template
  - _Requirements: 3.2, 10.4_

- [ ] 9. Create Development Scripts for Concurrent Servers
  - [ ] 9.1 Add concurrent dev script to `frontend/package.json`
    - Create script to run both backend and frontend dev servers
    - Use `concurrently` package (already installed)
    - Configure proper output formatting and colors
    - _Requirements: 7.1, 7.2, 7.3_

  - [ ] 9.2 Document manual startup commands in both READMEs
    - Document `php artisan serve` command for backend
    - Document `npm run dev` command for frontend
    - Document concurrent startup option
    - _Requirements: 7.1, 7.2, 7.3, 9.1, 9.2_

- [ ] 10. Create Production Build and Deployment Scripts
  - [ ] 10.1 Create `frontend/scripts/verify-build.js` validation script
    - Check that `dist/` directory exists after build
    - Verify `manifest.json` is generated
    - Validate manifest structure and entry points
    - Check that compiled assets exist
    - _Requirements: 2.2, 4.5_

  - [ ] 10.2 Add build verification to `frontend/package.json` scripts
    - Create `test:build` script that runs build + verification
    - _Requirements: 2.2, 7.4_

  - [ ] 10.3 Document that no asset copying is needed
    - Laravel reads directly from `frontend/dist/` via plugin
    - No deployment script needed (plugin handles paths)
    - Document that `VITE_BUILD_DIRECTORY` must point to frontend dist
    - _Requirements: 4.3, 7.4_

- [ ] 11. Migrate Remaining Backend Files and Verify Structure
  - Verify all controllers are in `backend/app/Http/Controllers/`
  - Verify all models are in `backend/app/Models/`
  - Verify all routes are in `backend/routes/`
  - Verify all migrations are in `backend/database/migrations/`
  - Verify all config files are in `backend/config/`
  - _Requirements: 1.1, 1.3, 8.1, 8.3_

- [ ] 12. Update Path References in Backend Code
  - Search for any hardcoded paths referencing old structure
  - Update any `resource_path()` calls that reference frontend files
  - Verify all Inertia::render() calls use correct component names
  - _Requirements: 3.1, 8.1_

- [ ] 13. Checkpoint - Test Development Mode
  - Start backend server: `cd backend && php artisan serve`
  - Start frontend dev server: `cd frontend && npm run dev`
  - Verify backend serves at `http://localhost:8000`
  - Verify frontend dev server runs at `http://localhost:5173`
  - Verify `hot` file is created in `frontend/dist/` by plugin
  - Check browser console for errors when loading `http://localhost:8000`
  - Verify HMR (Hot Module Replacement) works when editing React components
  - Verify plugin automatically detects dev server and loads from it
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 14. Test Authentication Flow in Development Mode
  - [ ] 14.1 Test user registration flow
    - Navigate to `/register` page
    - Fill in registration form and submit
    - Verify session cookie is set in browser
    - Verify redirect to dashboard after registration
    - _Requirements: 10.1, 10.2, 10.5_

  - [ ] 14.2 Test user login flow
    - Navigate to `/login` page
    - Enter valid credentials and submit
    - Verify session cookie is set
    - Verify redirect to dashboard
    - Verify `auth.user` prop is available in React components
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

  - [ ] 14.3 Test authenticated navigation
    - Navigate between dashboard and profile pages
    - Verify SPA behavior (no full page reloads)
    - Verify `auth.user` persists across page navigations
    - _Requirements: 10.3, 10.4_

  - [ ] 14.4 Test logout flow
    - Click logout button
    - Verify session is cleared
    - Verify redirect to home/login page
    - Verify subsequent requests show unauthenticated state
    - _Requirements: 10.5_

- [ ] 15. Create Backend Integration Tests
  - [ ]* 15.1 Write test for Inertia response structure
    - Test that dashboard route returns Inertia response
    - Verify response includes correct component name
    - Verify response includes auth.user prop
    - _Requirements: 3.2, 3.3, 10.4_

  - [ ]* 15.2 Write test for CORS configuration
    - Test that requests from frontend origin are allowed
    - Verify `Access-Control-Allow-Origin` header is set
    - Verify `Access-Control-Allow-Credentials` is true
    - _Requirements: 5.1, 5.4_

  - [ ]* 15.3 Write test for session persistence
    - Test that login creates valid session
    - Test that session persists across requests
    - Test that authenticated user data is available
    - _Requirements: 5.2, 10.3_

  - [ ]* 15.4 Write test for guest redirection
    - Test that unauthenticated users are redirected to login
    - Test that protected routes require authentication
    - _Requirements: 10.5_

- [ ] 16. Test Production Build Process
  - [ ] 16.1 Build frontend for production
    - Run `cd frontend && npm run build`
    - Verify `frontend/dist/` directory is created
    - Verify `manifest.json` exists in dist directory
    - Verify compiled JS and CSS files have content hashes
    - Verify plugin generates manifest in correct format
    - _Requirements: 2.2, 2.3, 4.4_

  - [ ] 16.2 Verify Laravel can read from frontend dist
    - Ensure `VITE_BUILD_DIRECTORY=../frontend/dist` in backend `.env`
    - Verify plugin can access manifest from this location
    - No asset copying needed (plugin reads directly)
    - _Requirements: 4.3, 4.4_

  - [ ] 16.3 Test production mode locally
    - Set `APP_ENV=production` in backend `.env`
    - Start backend server
    - Navigate to application in browser
    - Verify assets load without 404 errors
    - Verify plugin loads assets from `frontend/dist/`
    - Check that asset URLs include content hashes
    - _Requirements: 4.2, 4.3_

- [ ] 17. Create Documentation
  - [ ] 17.1 Create `backend/README.md` with backend documentation
    - Document project structure and purpose
    - Document environment variables and configuration
    - Document development commands (`php artisan serve`, `php artisan test`)
    - Document how `laravel-vite-plugin` handles asset loading automatically
    - Document deployment process (no asset copying needed)
    - _Requirements: 9.1, 9.3_

  - [ ] 17.2 Create `frontend/README.md` with frontend documentation
    - Document project structure (src/, public/, dist/)
    - Document environment variables
    - Document development commands (`npm run dev`, `npm run build`)
    - Document Vite configuration with `laravel-vite-plugin`
    - Document that plugin handles dev/production mode automatically
    - _Requirements: 9.2, 9.4_

  - [ ] 17.3 Create root `README.md` with architecture overview
    - Document the hybrid separated architecture
    - Explain how `laravel-vite-plugin` bridges backend and frontend
    - Document how to run both projects for development
    - Document the Inertia.js communication flow
    - Document production deployment workflow (build frontend, configure path)
    - Include troubleshooting section for common issues
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_

- [ ] 18. Add Environment Variable Validation
  - Add validation in `backend/app/Providers/AppServiceProvider.php`
  - Check for required variables: `FRONTEND_URL`, `VITE_BUILD_DIRECTORY`, `SESSION_DOMAIN`, `SANCTUM_STATEFUL_DOMAINS`
  - Throw descriptive error if critical variables are missing in production
  - Verify `frontend/dist/manifest.json` exists (plugin needs it)
  - _Requirements: 6.5, 4.5_

- [ ] 19. Create Troubleshooting Documentation
  - Document solution for "Vite dev server not running" error (check `hot` file)
  - Document solution for CORS errors
  - Document solution for session cookie not set
  - Document solution for missing manifest file in production
  - Document solution for asset 404 errors (check `VITE_BUILD_DIRECTORY` path)
  - Document solution for Inertia version mismatch
  - Document that plugin handles most configuration automatically
  - _Requirements: 9.5_

- [ ] 20. Final Checkpoint - Complete System Verification
  - [ ] 20.1 Verify development mode works end-to-end
    - Both servers start without errors
    - All pages load correctly
    - Authentication flow works completely
    - HMR updates work in real-time
    - No console errors in browser

  - [ ] 20.2 Verify production build works end-to-end
    - Frontend builds successfully
    - Laravel reads manifest from `frontend/dist/` via plugin
    - Application runs in production mode
    - All authentication flows work
    - Assets load with proper cache-busting

  - [ ] 20.3 Verify all requirements are met
    - Review requirements document
    - Confirm each acceptance criterion is satisfied
    - Document any deviations or known issues

  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional test-related sub-tasks and can be skipped for faster MVP
- Each task references specific requirements from the requirements document for traceability
- Checkpoints (tasks 13, 20) ensure incremental validation at critical milestones
- The implementation maintains backward compatibility with Laravel Breeze authentication
- CORS and session configuration are critical for cross-origin authentication to work
- The `laravel-vite-plugin` automatically handles dev/production mode detection via `hot` file
- Plugin reads manifest from `frontend/dist/` - no asset copying needed
- Development mode uses Vite dev server for HMR; production mode serves from dist
- All environment variables should be documented in .env.example files
- Hybrid architecture keeps Laravel compatibility while achieving physical separation

## Implementation Order Rationale

1. **Phase 1-2 (Tasks 1-2)**: Restructure frontend first to establish clean separation
2. **Phase 3 (Task 3)**: Configure backend to consume frontend assets
3. **Phase 4-5 (Tasks 4-5)**: Enable cross-origin communication (CORS + sessions)
4. **Phase 6-7 (Tasks 6-7)**: Configure environment and API client
5. **Phase 8-12 (Tasks 8-12)**: Wire up Inertia and verify file structure
6. **Phase 13-14 (Tasks 13-14)**: Test development mode thoroughly
7. **Phase 15-16 (Tasks 15-16)**: Add tests and verify production build
8. **Phase 17-19 (Tasks 17-19)**: Document everything
9. **Phase 20 (Task 20)**: Final verification and sign-off

This order ensures that each phase builds on the previous one, with testing checkpoints to catch issues early.
