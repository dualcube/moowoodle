import { render } from '@wordpress/element';
import MyClassroom from './MyClassroom';

document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("moowoodle-my-classroom");
    if (container) {
        render(<MyClassroom />, container);
    }
});
