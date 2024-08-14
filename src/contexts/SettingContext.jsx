import React, { createContext, useReducer, useContext } from 'react';

// setting context object.
const SettingContext = createContext();

/**
 * dispatch function for setting related operation.
 * @param {*} state  state variable.
 * @param {*} action name of action for state variable.
 * @returns 
 */
const settingReducer = ( state, action ) => {
    switch ( action.type ) {
        case 'SET_SETTINGS':
            return { ...action.payload };
        case 'UPDATE_SETTINGS':
            const { key, value } = action.payload;
            const setting = { ...state.setting, [key]: value };
            return { ...state, 'setting': setting };
        case 'CLEAR_SETTINGS':
            return { settingName: '', setting: {} };
        default:
            return state;
    }
};

/**
 * context provider component
 * @param {*} props 
 * @returns 
 */
const SettingProvider = ( props ) => {
    const [state, dispatch] = useReducer( settingReducer, { settingName: '', setting: {} } );

    /**
     * Set new setting data.
     * @param {*} name 
     * @param {*} setting 
     */
    const setSetting = ( settingName, setting ) => {
        dispatch( { type: 'SET_SETTINGS', payload: { settingName, setting } } );
    };

    /**
     * Update value to a existing setting data.
     * @param {*} key 
     * @param {*} value 
     */
    const updateSetting = ( key, value ) => {
        dispatch( { type: 'UPDATE_SETTINGS', payload: { key, value } } );
    }

    /**
     * Clear the setting to default.
     */
    const clearSetting = () => {
        dispatch( { type: 'CLEAR_SETTINGS' } );
    }

    return (
        <SettingContext.Provider value={{ ...state, setSetting, updateSetting, clearSetting }}>
            { props.children }
        </SettingContext.Provider>
    );
};

/**
 * get setting context.
 * @returns [ settingName, setting, setSetting, updateSetting, clearSetting ]
 */
const useSetting = () => {
    const context = useContext( SettingContext );
    if ( ! context ) {
        throw new Error('useSetting must be used within a SettingProvider');
    }
    return context;
};

export { SettingProvider, useSetting };
