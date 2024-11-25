import React, { useState, useEffect } from "react";
import { useLocation } from 'react-router-dom';

import Settings from "./components/Settings/Settings";
import Synchronization from "./components/Synchronization/Synchronization";
import Courses from "./components/Courses/Courses";
import Enrollment from "./components/Enrollment/Enrollment";

const App = () => {
    const location = new URLSearchParams( useLocation().hash );

    document.querySelectorAll('#toplevel_page_moowoodle>ul>li>a').forEach((element) => {
        const urlObject = new URL(element.href);
        const hashParams = new URLSearchParams(urlObject.hash.substring(1));

        element.parentNode.classList.remove('current');
        if ( hashParams.get('tab') === location.get('tab')) {
            element.parentNode.classList.add('current');
        }
    });

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
