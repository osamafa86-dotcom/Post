import './bootstrap';
import { LivewireSortable } from 'livewire-sortable';

document.addEventListener('livewire:load', () => {
    LivewireSortable.init();
});
