import { useEffect, useState } from "react";


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
        <div>
            {
                selectedFields.map(([wpField, mwField], index) => {
                    return (
                        <div>
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
                                onClick={(e) => {
                                    e.preventDefault();
                                    removeSelectedFields( index );
                                }}
                            >-</button>
                        </div>
                    );
                })
            }
            <div>
                <button
                    className=""
                    onClick={(e) => {
                        e.preventDefault();
                        insertSelectedFields();
                    }}
                >+</button>
            </div>
        </div>
    );
}

export default SyncMap;