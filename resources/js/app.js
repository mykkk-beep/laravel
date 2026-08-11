import './bootstrap';

import Alpine from 'alpinejs';

import { Html5Qrcode } from 'html5-qrcode';
window.Html5Qrcode = Html5Qrcode; // so scan.blade.php's existing code still works unchanged

window.Alpine = Alpine;

Alpine.start();
