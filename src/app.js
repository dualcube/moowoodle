import React, { useState, useEffect } from "react";
import { useLocation } from 'react-router-dom';

import Settings from "./components/Settings/Settings";
import Synchronization from "./components/Synchronization/Synchronization";
import Courses from "./components/Courses/Courses";
import Enrollment from "./components/Enrollment/Enrollment";

// import Courses from "./components/SubMenuPage/AllCourses";

// import ManageEnrolment from "./commponents/SubMenuPage/ManageEnrolment";
// import Synchronization from "./commponents/SubMenuPage/Synchronization";

// import SideBanner from "./commponents/Common/SideBanner";
// import ProOverlay from "./commponents/Common/ProOverlay";
// import dualcubeLogo from "./assets/images/dualcube.png";

// css and scss file for global styling.
// import "./styles/admin.css";

// utils js file for global customisation.
// import "./utils/moowoodle-admin-frontend.js";

const App = () => {
    const currentUrl = window.location.href;
    document.querySelectorAll('#toplevel_page_moowoodle>ul>li>a').forEach((element) => {
        element.parentNode.classList.remove('current');
        if (element.href === currentUrl) {
            element.parentNode.classList.add('current');
        }
    });

    const location = new URLSearchParams( useLocation().hash );

    return (
        <>
            { location.get('tab') === 'settings' && <Settings initialTab='general' /> }
            { location.get('tab') === 'synchronization' && <Synchronization initialTab='connection' /> }
            { location.get('tab') === 'all-courses' && <Courses /> }
            { location.get('tab') === 'manage-enrolment' && <Enrollment /> }
        </>
    );
}

export default App;
