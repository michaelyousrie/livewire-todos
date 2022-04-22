require('./bootstrap');

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.confirmCallback = ($msg, $callback, ...$params) => confirm($msg) ? $callback($params) : false;
