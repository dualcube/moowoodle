import React, { useState, useEffect } from "react";
import { useLocation } from 'react-router-dom';

import Settings from "./components/Settings/Settings";
import { ModuleProvider } from './contexts/ModuleContext.jsx';
import Synchronization from "./components/Synchronization/Synchronization";
import Courses from "./components/Courses/Courses";
import Cohorts from "./components/Cohorts/Cohorts";
import Enrollment from "./components/Enrollment/Enrollment";

const Route = () => {
    const currentTab = new URLSearchParams(useLocation().hash);
    return (
        <>
            { currentTab.get('tab') === 'settings' && <Settings initialTab='general' /> }
            { currentTab.get('tab') === 'synchronization' && <Synchronization initialTab='connection' /> }
            { currentTab.get('tab') === 'courses' && <Courses /> }
            { currentTab.get('tab') === 'cohorts' && <Cohorts /> }
            { currentTab.get('tab') === 'enrolments' && <Enrollment /> }
        </>
    );
}

const App = () => {
    const currentTabParams = new URLSearchParams(useLocation().hash);
    
    document.querySelectorAll('#toplevel_page_moowoodle>ul>li>a').forEach((menuItem) => {
        const menuItemUrl = new URL(menuItem.href);
        const menuItemHashParams = new URLSearchParams(menuItemUrl.hash.substring(1));

        menuItem.parentNode.classList.remove('current');
        if ( menuItemHashParams.get('tab') === currentTabParams.get('tab')) {
            menuItem.parentNode.classList.add('current');
        }
    });
   
    return (
        <>
            <ModuleProvider modules = {appLocalizer.active_modules}><Route/></ModuleProvider>
        </>
    )
}

export default App;
