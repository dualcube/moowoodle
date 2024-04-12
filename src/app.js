import React, { useState, useEffect } from "react";
import { useLocation } from 'react-router-dom';

import Settings from "./components/Settings/Settings";

// import AllCourses from "./commponents/SubMenuPage/AllCourses";
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
            { location.get('tab') === 'moowoodle-settings' && <Settings initialTab='general' /> }
        </>
    );
}

export default App;
