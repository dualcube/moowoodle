import { render } from '@wordpress/element';
import { BrowserRouter} from 'react-router-dom';
import MyClassroom from './MyClassroom';



// Render the App component into the DOM
render(<BrowserRouter><MyClassroom /></BrowserRouter>, document.getElementById('moowoodle-my-classroom'));