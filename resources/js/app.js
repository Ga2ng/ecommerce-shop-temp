import './bootstrap';

// Import jQuery
import $ from 'jquery';

// Make jQuery available globally
window.$ = window.jQuery = $;

// Import Alpine.js
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Start Alpine.js
Alpine.start();

// Export jQuery for use in other modules if needed
export { $ as jQuery };
