import 'trix';
import 'trix/dist/trix.css';
import './app';

// Trix: no file attachments (images are managed through the Media library).
document.addEventListener('trix-file-accept', (e) => e.preventDefault());
