import { render } from '@wordpress/element';
import MyCourse from './MyCourse.jsx';

document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("moowoodle-my-course");
    if (container) {
        render(<MyCourse />, container);
    }
});
