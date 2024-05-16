import { useEffect, useState } from "react";
import './SyncMap.scss';

const SyncMap = (props) => {
    const { value, onChange } = props;
    const wordpressSyncFields = [ 'firstname', 'lastname', 'username', 'password' ];
    const moodleSyncFields    = [ 'firstname', 'lastname', 'username', 'password' ];
    
    const [ selectedFields, setSelectedFields ] = useState( value || [] );
    
    const [wordpressSyncFieldsChose, setWordpressSyncFieldsChose] = useState(wordpressSyncFields);
    const [moodleSyncFieldsChose, setMoodleSyncFieldsChose] = useState(wordpressSyncFields);

    // Get all unselected fields for a site.
    const getUnselectedFields = ( site ) => {
        const unSelectFields = [];

        let syncFields = [];

        if ( site === 'wordpress' ) {
            syncFields = wordpressSyncFields;
        }
        if (site === 'moodle') {
            syncFields = moodleSyncFields;
        }

        syncFields.forEach(( syncField ) => {
            // Check wordpress field is present in the selected fields or not
            let hasSelect = false;

            selectedFields.forEach( ( [ wpField, mwField ] ) => {
                if (site === 'wordpress' && wpField === syncField) hasSelect = true;
                if (site === 'moodle' && mwField === syncField) hasSelect = true;
            });
            
            if ( ! hasSelect ) {
                unSelectFields.push( syncField );
            }
        });

        return unSelectFields;
    }

    // Change a particular selected fields.
    const changeSelectedFields = ( fieldIndex, value, site ) => {
        setSelectedFields((selectedFields) => {
            return selectedFields.map( ( fieldPair, index ) => {
                if ( index == fieldIndex ) {
                    if ( site == 'wordpress' ) {
                        fieldPair[0] = value;
                    }
                    if ( site == 'moodle' ) {
                        fieldPair[1] = value;
                    }
                }
                return fieldPair
            });
        })
    }

    // Remove a particular selected fields
    const removeSelectedFields = ( fieldIndex ) => {
        setSelectedFields((selectedFields) => {
            return selectedFields.filter( ( fieldPair, index ) => index != fieldIndex );
        })
    }

    const insertSelectedFields = () => {
        if ( wordpressSyncFieldsChose.length && moodleSyncFieldsChose.length ) {
            const wpField = wordpressSyncFieldsChose.shift();
            const mdField = moodleSyncFieldsChose.shift();
           
            console.log( wpField, mdField );

            setSelectedFields(( selectedFields ) => {
                return [ ...selectedFields, [ wpField, mdField ] ];
            });
        } else {
            console.log( 'unable to add sync fields' );
        }
    }
    
    useEffect(() => {
        // console.log(selectedFields);
        setWordpressSyncFieldsChose( getUnselectedFields( 'wordpress' ) );
        setMoodleSyncFieldsChose( getUnselectedFields( 'moodle' ) );
    }, [selectedFields] );

    return (
        <div className="sync-map-container">
            {
                selectedFields.map(([wpField, mwField], index) => {
                    return (
                        <div className="map-content-wrapper">
                            {/* Wordpress select */}
                            <select 
                                className=""
                                onChange={(e) => changeSelectedFields( index, e.target.value, 'wordpress' ) }
                            >
                                <option value={wpField} selected>{wpField}</option>
                                {
                                    wordpressSyncFieldsChose.map((option) => {
                                        return <option value={option}>{option}</option>
                                    })
                                }
                            </select >

                            {/* Moodle select */}
                            <select 
                                className=""
                                value={mwField}
                                onChange={(e) => changeSelectedFields( index, e.target.value, 'moodle' ) }
                            >
                                <option value={mwField} selected>{mwField}</option>
                                {
                                    moodleSyncFieldsChose.map((option) => {
                                        return <option value={option}>{option}</option>
                                    })
                                }
                            </select >
                            <button
                                className="remove-mapping"
                                onClick={(e) => {
                                    e.preventDefault();
                                    removeSelectedFields( index );
                                }}
                            >
                                <span class="text">Clear</span>
                                <span class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z"></path></svg>
                                </span>
                            </button>
                        </div>
                    );
                })
            }
            <div>
                <button
                    className="add-mapping"
                    onClick={(e) => {
                        e.preventDefault();
                        insertSelectedFields();
                    }}
                >
                    <span class="text">Add</span>
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    );
}

export default SyncMap;