import React, { createContext, useReducer, useContext } from 'react';

// module context object.
const ModuleContext = createContext();

/**
 * dispatch function for module related operation.
 * @param {*} state  state variable.
 * @param {*} action name of action for state variable.
 * @returns 
 */
const ModuleReducer = ( state, action ) => {
    switch ( action.type ) {
        case 'INSERT_MODULE':
            return [ ...state, action.payload ];
        case 'DELETE_MODULE':
            return state.filter( ( module ) => module !== action.payload );
        default:
            return state;
    }
};

/**
 * context provider component
 * @param {*} props 
 * @returns 
 */
const ModuleProvider = ( props ) => {
    const [ modules, dispatch ] = useReducer( ModuleReducer, props.modules );

    /**
     * Insert a new module.
     * @param {String} moduleName
     */
    const insertModule = ( moduleName ) => {
        dispatch( { type: 'INSERT_MODULE', payload: moduleName } );
    };

    /**
     * Remove module from module list.
     * @param {String} moduleName
     */
    const removeModule = ( moduleName ) => {
        dispatch( { type: 'DELETE_MODULE', payload: moduleName } );
    }

    return (
        <ModuleContext.Provider value={ { modules, insertModule, removeModule } }>
            { props.children }
        </ModuleContext.Provider>
    );
};

/**
 * get module context.
 * @returns [ modules, insertModule, removeModule ]
 */
const useModules = () => {
    const context = useContext( ModuleContext );
    if ( ! context ) {
        throw new Error( 'useModule must be used within a ContextProvider' );
    }
    return context;
};

export { ModuleProvider, useModules };