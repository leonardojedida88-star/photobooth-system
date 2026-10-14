SNAPSTUDIO PHOTOBOOTH — XAMPP SETUP
===================================

FEATURES
- Webcam live preview with permission prompt
- 3, 5, or 10 second countdown
- 2, 3, or 4 photos per session
- Original, Mono, Retro, Pop, and Soft filters
- Classic strip, mini strip, and 2x2 grid layouts
- Custom strip background and caption
- Download finished strip as PNG
- Browser print dialog
- Save strips to the local uploads folder and MySQL gallery

REQUIREMENTS
- Windows PC/laptop
- XAMPP with Apache, MySQL, PHP
- Chrome or Microsoft Edge
- Built-in or USB webcam

INSTALL
1. Extract the "photobooth_system" folder to:
   C:\xampp\htdocs\photobooth_system
   (If XAMPP is installed elsewhere, use that htdocs folder.)

2. Open XAMPP Control Panel. Start Apache and MySQL.

3. Open:
   http://localhost/phpmyadmin

4. Click Import, choose database.sql from this project, then click Import/Go.
   This creates the photobooth_db database and photos table.

5. Check config.php. Default XAMPP credentials are user root and blank password.
   If you configured a MySQL password, update $password.

6. Ensure the uploads folder exists and PHP can write to it.
   It is included in the project. If missing, create a folder named uploads.
   On normal local XAMPP installations, default folder permissions are enough.

7. Open this URL in Chrome or Edge:
   http://localhost/photobooth_system/

8. Click "Enable camera" and allow camera permission. Choose filter, layout, countdown,
   and number of photos, then click "Start photo session".

IMPORTANT CAMERA NOTES
- Browser webcam access works on localhost. Do not open index.php by double-clicking it;
  use the http://localhost URL.
- If camera permission was denied, click the icon beside the browser address and allow Camera.
- Close Zoom/Teams or other programs that may be using the webcam.
- This app does not record audio.
- Camera preview is mirrored for a natural selfie view; captured images are mirrored to match.
- Photos are stored locally in uploads/ and their filenames/timestamps in MySQL.
- Anyone with access to this computer/server folder may be able to view saved photos.
  Delete unwanted photos from the gallery and uploads folder manually.
- Print uses the browser print dialog. Choose your printer and paper size there.
  The final size depends on printer settings; select fit-to-page if needed.
- Google Fonts are optional styling; the app still works without internet, using fallback fonts.

TROUBLESHOOTING
- Database connection failed: start MySQL, import database.sql, check config.php.
- Gallery save failed: confirm uploads/ is writable and database was imported.
- No camera: use Chrome/Edge at localhost, allow camera permission, check webcam is connected.
- Blank/unstyled page: keep assets/ folder beside index.php and refresh the page.
