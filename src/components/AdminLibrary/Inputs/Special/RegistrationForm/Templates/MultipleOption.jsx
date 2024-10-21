import React, { useState, useEffect, useRef } from "react";
import { ReactSortable } from "react-sortablejs";
import { FaArrowsAlt } from 'react-icons/fa';
import OptionMetaBox from "./OptionMetaBox";

const MultipleOptions = (props) => {
    const { formField, onChange, selected } = props;
    const settingHasChanged = useRef(false);
    const firstTimeRender = useRef(true);
    const [openOption, setOpenOption] = useState(null);

    const [options, setOptions] = useState(() => {
        if (Array.isArray(formField.options) && formField.options.length) {
            return formField.options;
        }
        return [];
    });

    useEffect(() => {
        if (settingHasChanged.current) {
            settingHasChanged.current = false;
            onChange('options', options);
        }
    }, [options]);

    ///////////////////////////////////////// Functionality for button change /////////////////////////////////////////
    
    /**
     * Handle the field change of option
     */
    const handleOptionFieldChange = (index, key, value) => {
        // copy the form field before modify
        const newOptions = [ ...options ]
        
        // Update the new form field list
        newOptions[index] = {
            ...newOptions[index],
            [key]: value
        }

        // Update the state variable
        setOptions(newOptions);
    }

    /**
     * Insert a new option at end of option list
     */
    const handleInsertOption = () => {
        setOptions([...options, {
            label: 'I am label',
            value: 'value'
        }]);
    }

    /**
     * Handle the delete a particular option options
     * it will not work if only one option is set
     */
    const handleDeleteOption = (index) => {
        if (options.length <= 1) {
            return;
        }
        
        // Create a new array without the element at the specified index
        const newOptions = options.filter((_, i) => i !== index);

        // Update the state with the new array
        setOptions(newOptions);
    }

    return (
        <div className="main-input-wrapper">
            {/* Render label */}
            <input
                className="input-label multipleOption-label"
                type="text"
                value={formField.label}
                placeholder={"I am label"}
                onChange={(event) => {
                    onChange('label', event.target.value);
                }}
            />

            {/* Render Options */}
            <ReactSortable
                className="multiOption-wrapper"
                list={options}
                setList={(newList) => { 
                    if (firstTimeRender.current) {
                        firstTimeRender.current = false;
                        return;
                    }
                    setOptions(newList)
                }}
                handle=".drag-handle-option"
            >
                {
                    options && options.map((option, index) => (
                        <div className="option-list-wrapper drag-handle-option">
                            {/* Render label modifire */}
                            <div className="option-label">
                                <input
                                    type="text"
                                    value={option.label}
                                    onChange={(event) => {
                                        settingHasChanged.current = true;
                                        handleOptionFieldChange(index, 'label', event.target.value);
                                    }}
                                    readOnly
                                    onClick={(event) => {
                                        setOpenOption(index)
                                        event.stopPropagation()
                                    }}
                                />
                            </div>
                            
                            {/* Render grab icon for drag and drop */}
                            <div className="option-control-section">
                                {/* Render delete option */}
                                <div onClick={() => {settingHasChanged.current = true;
                                handleDeleteOption(index)}}>Delete</div>
                                {/* Render setting option */}
                                <OptionMetaBox
                                    hasOpen={openOption === index}
                                    option={option}
                                    onChange={(key, value) => {
                                        settingHasChanged.current = true;
                                        handleOptionFieldChange(index, key, value)}
                                    }
                                    setDefaultValue={() => {
                                        if (option.isdefault) {
                                            settingHasChanged.current = true;
                                            handleOptionFieldChange(index, 'isdefault', false);
                                        } else {
                                            let defaultValueIndex = null;
                                            options.forEach((option, index) => {
                                                if (option.isdefault) {
                                                    defaultValueIndex = index;
                                                }
                                            });
                                            if (defaultValueIndex !== null) {
                                                settingHasChanged.current = true;
                                                handleOptionFieldChange(defaultValueIndex, 'isdefault', false);
                                            }
                                            settingHasChanged.current = true;
                                            handleOptionFieldChange(index, 'isdefault', true);

                                            // copy the form field before modify
                                            const newOptions = [ ...options ]
                                            
                                            // Update the new form field list
                                            newOptions[defaultValueIndex] = { ...newOptions[defaultValueIndex], isdefault: false }
                                            newOptions[index] = { ...newOptions[index], isdefault: true }

                                            settingHasChanged.current = true;
                                            // Update the state variable
                                            setOptions(newOptions);
                                        }
                                    }}
                                />
                            </div>
                        </div>
                    ))
                }

                {/* Render add option at last */}
                <div className="add-more-option-section" onClick={() => {
                    settingHasChanged.current = true;
                    handleInsertOption()}
                    }>
                    Add new options <span><i className="admin-font font-support"></i></span>
                </div>
            </ReactSortable>
        </div>
    )
}

export default MultipleOptions;