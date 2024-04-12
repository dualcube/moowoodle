/**
 * Core static JSON service module.
 */

/**
 * Get Setting JSON data as object.
 * @return {Array} Array of Object.
 */
const getTemplateData = () => {

    // Required the context.
    const context = require.context( '../template/settings', true, /\.js$/ ); // Adjust the folder path and file extension

    // Prepare the structure here...
    function importAll(context) {
        const folderStructure = [];
      
        context.keys().forEach(key => {
            const path = key.substring(2); // Remove './' from the beginning of the path
            const parts = path.split('/');
            const fileName = parts.pop();
            let currentFolder = folderStructure;
      
            // Traverse the folder structure and create objects
            parts.forEach(folder => {
                let folderObject = currentFolder.find( item => item.name === folder && item.type === 'folder' );
                if ( ! folderObject ) {
                    folderObject = { name: folder, type: 'folder', content: [] };
                    currentFolder.push(folderObject);
                }
                currentFolder = folderObject.content;
            });
      
            // Add the file to the appropriate folder
            currentFolder.push({ name: fileName.replace('.js', ''), type: 'file', content: context(key).default });
        });

        return folderStructure;
    }
    
    const settings = importAll( context );
    
    // Debugg here...
    // console.log(settings);

    return settings;
};

export { getTemplateData };