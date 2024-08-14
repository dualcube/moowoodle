import React, { createContext, useReducer, useContext } from 'react';

// theme context object.
const ThemeContext = createContext();

/**
 * dispatch function for theme related operation.
 * @param {*} state  state variable 
 * @param {*} action name of action for state variable.
 * @returns 
 */
const themeReducer = ( state, action ) => {
    switch ( action.type ) {
        case 'TOGGLE_THEME':
            return { ...state, theme: state.theme === 'light' ? 'dark' : 'light' };
        default:
            return state;
    }
};

/**
 * context provider component
 * @param {*} props 
 * @returns 
 */
const ThemeProvider = ( props ) => {
    const [state, dispatch] = useReducer( themeReducer, { theme: 'light' } );

    /**
     * toggle the theme if dark then toogle to light. vice versa.
     */
    const toggleTheme = () => {
        dispatch( { type: 'TOGGLE_THEME' } );
    };

    return (
        <ThemeContext.Provider value={{ ...state, toggleTheme }}>
            { props.children }
        </ThemeContext.Provider>
    );
};

/**
 * get theme context.
 * @returns [ state, toggleTheme ]
 */
const useTheme = () => {
    const context = useContext( ThemeContext );
    if ( ! context ) {
        throw new Error('useTheme must be used within a ThemeProvider');
    }
    return context;
};

export { ThemeProvider, useTheme };
