/* global appLocalizer */

import { useLocation } from "react-router-dom";
import DynamicForm from "../AdminLibrary/DynamicForm/DynamicForm";
import Tabs from '../AdminLibrary/Tabs/Tabs';
import Support from "../AdminLibrary/Support/Support";
import BannerSection from '../Banner/banner';
import SyncNow from "../SyncNow/SyncNow";

// import context
import { SettingProvider, useSetting } from "../../contexts/SettingContext";

// import services function
import { getApiLink, sendApiResponse } from "../../services/apiService";
import { getTemplateData } from "../../services/templateService";

// import utility function
import { getAvialableSettings, getSettingById } from "../../utiles/settingUtil";
import { useState, useEffect } from "react";

const Synchronization = () => {

    // get all setting
    const settingsArray = getAvialableSettings(getTemplateData( 'synchronizations' ), []);

    // get current browser location
    const location = new URLSearchParams( useLocation().hash );

    // Render the dinamic form.
    const getForm = (currentTab) => {

        // get the setting context
        const { setting, settingName, setSetting } = useSetting();
        
        const settingModal = getSettingById( settingsArray, currentTab );

        if ( settingName != currentTab ) {
            setSetting( currentTab, appLocalizer.preSettings[currentTab] || {} );
        }

        useEffect(() => {
            appLocalizer.preSettings[settingName] = setting;
        }, [setting]);

        if ( currentTab === 'sync_now' ) {
            return (
                <SyncNow />
            );
        }

        return (
            <>
                { settingName === currentTab ? <DynamicForm setting={ settingModal } proSetting={appLocalizer.pro_settings_list} /> : <>Loading</> }
            </>
        );
    }

    return (
        <>
            <SettingProvider>
                <Tabs
                    tabData={ settingsArray }
                    currentTab={ location.get( 'sub-tab' ) }
                    getForm={getForm}
                    BannerSection = { ! appLocalizer.pro_active && BannerSection}
                    prepareUrl={(subTab) => `?page=moowoodle#&tab=synchronization&sub-tab=${subTab}` }
                />
            </SettingProvider>
        </>
    );
}

export default Synchronization;