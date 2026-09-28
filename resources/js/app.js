import './bootstrap';

import Alpine from 'alpinejs';
import { initSearch } from './search';

window.Alpine = Alpine;

Alpine.start();

initSearch();
