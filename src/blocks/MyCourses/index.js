import { render } from '@wordpress/element';
import { BrowserRouter} from 'react-router-dom';
import MyCourse from './MyCourse.jsx';


// Render the App component into the DOM
render(<BrowserRouter><MyCourse /></BrowserRouter>, document.getElementById('moowoodle-my-course'));