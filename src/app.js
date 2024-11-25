import React, { useState, useEffect } from "react";
import { useLocation } from 'react-router-dom';

import Settings from "./components/Settings/Settings";
import Synchronization from "./components/Synchronization/Synchronization";
import Courses from "./components/Courses/Courses";
import Enrollment from "./components/Enrollment/Enrollment";

const App = () => {
    const getBaseUrl = (url) => url.split('&sub-tab=')[0];
    const currentUrl = window.location.href;
    document.querySelectorAll('#toplevel_page_moowoodle>ul>li>a').forEach((element) => {
        element.parentNode.classList.remove('current');
        if (getBaseUrl(element.href) === getBaseUrl(currentUrl)) {
            element.parentNode.classList.add('current');
        }
    });

    const location = new URLSearchParams( useLocation().hash );

    return (
        <>
            { location.get('tab') === 'settings' && <Settings initialTab='general' /> }
            { location.get('tab') === 'synchronization' && <Synchronization initialTab='connection' /> }
            { location.get('tab') === 'courses' && <Courses /> }
            { location.get('tab') === 'enrolments' && <Enrollment /> }
        </>
    );
}

export default App;
