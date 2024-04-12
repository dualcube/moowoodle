/**
 * Get settring objeccts as array of object sorted order based on priority.
 * @param {Array} settings 
 * @returns {Array}
 */
const getSettingsByPriority = ( settings ) => {
    
    if ( Array.isArray( settings ) ) {
        
        settings.sort((firstSet, secondSet) => {
            
            // Variable contain priority of two compairable element.
            let firstPriority   = 0;
            let secondPriority  = 0;

            // First check if the settings have child.
            // First Recursively sort the childs !!!importent.
            if ( firstSet.type == 'folder' ) {
                firstSet.content = getSettingsByPriority( firstSet.content );
                
                // Set first child's priority to parent's priority
                const firstChild = firstSet.content[0];
                firstPriority = firstChild.content.priority;

            } else {
                firstPriority = firstSet.content.priority;
            }

            if (secondSet.type === 'folder') {
                secondSet.content = getSettingsByPriority( secondSet.content );
                
                // Set first child's priority to parent's priority
                const firstChild = secondSet.content[0];
                secondPriority = firstChild.content.priority;

            } else {
                secondPriority = secondSet.content.priority
            }

            return firstPriority - secondPriority;
        });
    }

    return settings;
}

/**
 * Get all setting that's id is present in provided ids array.
 * @param {*} settings 
 * @param {*} ids 
 * @returns filter setting.
 */
const filterSettingByIds = (settings, ids) => {

    if (Array.isArray(settings) && Array.isArray(ids)) {
        
        // Filter the array here
        const filterSettings = [];

        for( let setting of settings ) {

            // Setting has child
            if (setting.type === 'folder') {

                // Prepare all childs recursivelly
                const settingContent = filterSettingByIds(setting.content, ids);

                if (settingContent.length) {
                    // Insert by deep copy. Otherwise it will change original object.
                    filterSettings.push( { ...setting, 'content': settingContent } );                 
                }

                continue;
            }

            if ( ids.includes( setting.content.id ) ) {
                filterSettings.push( setting );
            }

        }

        return filterSettings;
    }

    return settings;
}

/**
 * Get default settings from all settings.
 * @param {*} settings
 * @returns {*}
 */
const getDefaultSettings = ( settings ) => {

    if ( Array.isArray( settings ) ) {
        
        // Filter the array here
        const filterSettings = [];

        settings.forEach(setting => {
            
            // Setting has childs
            if (setting.type === 'folder') {

                // Prepare all childs recursivelly
                setting.content = getDefaultSettings( setting.content );

                if ( setting.content.length ) {
                    filterSettings.push( setting );                    
                }

                return;
            }

            // Append setting if its content is not empty and free setting
            if (
                ! setting.content.pro_dependent &&
                ! setting.content.module_dependent
            ) {
                filterSettings.push( setting );
            }
        });

        return filterSettings;
    }

    return settings;
}

/**
 * Get avialable settings include free settings and settings of provided ids.
 * @param {*} settings 
 * @param {*} ids 
 * @returns 
 */
const getAvialableSettings = (settings, ids = []) => {
    return getSettingsByPriority( [ ...getDefaultSettings( settings ) , ...filterSettingByIds( settings, ids ) ] );
}

/**
 * Get setting object from provided settings array matched the settingId.
 * If provided Id does not match it return empty array.
 * @param {*} settings 
 * @param {*} settingId 
 * @returns 
 */
const getSettingById = ( settings, settingId ) => {
    if ( Array.isArray( settings ) ) {

        // Iterate through all settings
        for ( let setting of settings ) {
            
            // If setting has child first search setting in childs
            if ( setting.type === 'folder' ) {
                
                // Get settingContent from child recursivlly
                const settingContent = getSettingById( setting.content, settingId );

                // If settingContent has found
                if ( Object.keys(settingContent).length ) {
                    return settingContent;
                }

                continue;
            }

            // If id matched
            if (setting.content.id === settingId) {
                return setting.content;
            }
        }
    }

    return [];
}

/**
 * Check if a setting is active or not.
 * @param {*} setting
 * @param {boolean} proActive
 * @param {Array} ids 
 * @return {boolean}
 */
const isActiveSetting = ( setting, proActive, ids ) => {
    // Default setting return true.
    if ( ! setting.module_dependent ) {
        return true;
    }

    // Module setting
    if (ids.includes(setting.id)) {
        
        // Free module setting return true.
        if ( ! setting.pro_dependent ) {
            return true;
        }

        // Pro module setting and pro is active return true.
        if ( proActive ) {
            return true;
        }
    }

    return false;
}

export { getAvialableSettings, getSettingById, isActiveSetting };