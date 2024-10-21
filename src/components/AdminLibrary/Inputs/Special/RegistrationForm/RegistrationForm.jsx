import React, { useState, useEffect, useRef } from "react";
import { ReactSortable } from "react-sortablejs";
import { FaArrowsAlt } from 'react-icons/fa';
import Template from "./Templates";
import './RegistrationForm.scss'
import Elements from "./Templates/elements";
import SettingMetaBox from "./Templates/MetaBox";
import ButtonCustomizer from "../ButtonCustomizer";

import { useSetting } from "../../../../../contexts/SettingContext"; 

// Set deault values for indivisual inputs.
const DEFAULT_OPTIONS = [
    {
        label: 'I have a bike',
        value: 'bike'
    },
    {
        label: 'I have a car',
        value: 'car'
    },
    {
        label: 'I have a boat',
        value: 'boat'
    }
];
const DEFAULT_PLACEHOLDER = 'I am default place holder';
const DEFAULT_LABEL_SIMPLE = 'I am default simple label';
const DEFAULT_LABEL_SELECT = 'I am default select label';
const DEFAULT_FORM_TITLE = 'I am default form title';

const selectOptions = [
    {
        icon: 'icon-form-textbox',
        value: 'text',
        label: 'Textbox'
    },
    {
        icon: 'icon-form-email',
        value: 'email',
        label: 'Email'
    },
    {
        icon: 'icon-form-textarea',
        value: 'textarea',
        label: 'Textarea'
    },
    {
        icon: 'icon-form-checkboxes',
        value: 'checkboxes',
        label: 'Checkboxes'
    },
    {
        icon: 'icon-form-multi-select',
        value: 'multiselect',
        label: 'Multi Select'
    },
    {
        icon: 'icon-form-radio',
        value: 'radio',
        label: 'Radio'
    },
    {
        icon: 'icon-form-dropdown',
        value: 'dropdown',
        label: 'Dropdown'
    },
    {
        icon: 'icon-form-recaptcha',
        value: 'recapta',
        label: 'Recapta'
    },
    {
        icon: 'icon-form-attachment',
        value: 'attachment',
        label: 'Attachment'
    },
    {
        icon: 'icon-form-section',
        value: 'section',
        label: 'Section'
    },
    {
        icon: 'icon-form-store-description',
        value: 'datepicker',
        label: 'Date Picker'
    },
    {
        icon: 'icon-form-address01',
        value: 'timepicker',
        label: 'Time Picker'
    },
    {
        icon: 'icon-form-address01',
        value: 'divider',
        label: 'Divider'
    },
];

/**
 * Component that render action section for add new
 */
const AddNewBtn = (props) => {
    const { onAddNew, learge } = props;

    return (
        <>
            {
                learge ?
                    <div
                        className={`addnew`}
                    >
                        <div onClick={(event) => { onAddNew?.() }}>
                            <i className="admin-font font-move"></i>
                        </div>
                        <p>Click to add new field</p>
                    </div>
                    :
                    <div className="add-new-sections" onClick={(event) => { onAddNew?.() }} >
                        <div>
                            <span>
                                <i className="admin-font font-move"></i>
                            </span>
                        </div>
                    </div>
            }
        </>
    )
}

/**
 * Component that render delete button section
 */
const DeleteBtn = (props) => {
    const { onDelete } = props;
    const { hideDelete } = props;

    return (
        <>
            <div
                className={`delete ${hideDelete ? 'disable' : ''}`}
                onClick={(event) => onDelete?.()}
            >
                <i className="admin-font font-close"></i>
            </div>
        </>
    )
}

// props value 
// 1. formTitlePlaceholder
// 2. formTitleDescription
// 3. formFieldTypes

const CustomFrom = (props) => {
    const { onChange, name, proSettingChange } = props;
    ////////////// Define state variable here /////////////////

    const { setting } = useSetting();
    const formSetting = setting[ name ] || {};
    
    const settingHasChanged = useRef(false);
    const firstTimeRender = useRef(true);

    // Contain list of selected form fields.
    const [formFieldList, setFormFieldList] = useState(() => {
        // Form field list can't be empty it should contain atlest form title.
        // This action prevent any backend action for empty form field list.

        let inputList = formSetting[ 'formfieldlist' ] || [];

        if (!Array.isArray(inputList) || inputList.length <= 0) {
            return [{
                id: 1,
                type: 'title',
                label: DEFAULT_FORM_TITLE,
                required: true,
            }];
        }

        return inputList;
    });

    const [buttonSetting, setButtonSetting] = useState( formSetting[ 'butttonsetting' ] || {} );

    // State for hold id of opend input section.
    const [opendInput, setOpendInput] = useState(null);

    // State variable for a random maximum id
    const [randMaxId, setRendMaxId] = useState();

    // Prepare random maximum id
    useEffect(() => {
        setRendMaxId(
            formFieldList.reduce((maxId, field) => Math.max(maxId, field.id), 0) + 1
        );
    }, [])

    // Save button setting and formfieldlist setting
    useEffect(() => {
        if (settingHasChanged.current) {
            settingHasChanged.current = false;
            onChange({
                'formfieldlist': formFieldList,
                'butttonsetting': buttonSetting
            })
        }
    }, [buttonSetting, formFieldList]);

    ////////////// Define functionality here /////////////////

    /**
     * Function generate a empty form field and return it.
     * By default it set the type to simple text
     */
    const getNewFormField = (type = 'text') => {
        const newFormField = {
            id: randMaxId,
            type: type,
            label: '',
            required: false,
        };

        switch (type) {
            case 'multiselect':
            case 'radio':
            case 'dropdown':
            case 'checkboxes':
                newFormField['label'] = DEFAULT_LABEL_SELECT;
                newFormField['options'] = DEFAULT_OPTIONS;
                break;
            default:
                newFormField['label'] = DEFAULT_LABEL_SIMPLE;
                newFormField['placeholder'] = DEFAULT_PLACEHOLDER;
                break;
        }

        // update randMaxId by 1
        setRendMaxId(randMaxId + 1);

        return newFormField;
    };

    /**
     * Function that append a new form field after a perticular index.
     * If form field list is empty it append at begining of form field list.
     */
    const appendNewFormField = (index, type = 'text') => {
        if (proSettingChange()) return;
        const newField = getNewFormField(type);

        // Create a new array with the new element inserted
        const newFormFieldList = [
            ...formFieldList.slice(0, index + 1),
            newField,
            ...formFieldList.slice(index + 1)
        ];

        // Update the state with the new array
        settingHasChanged.current = true;
        setFormFieldList(newFormFieldList);

        return newField;
    };

    /**
     * Function that delete a particular form field
     * @param {*} index 
     */
    const deleteParticularFormField = (index) => {
        
        if (proSettingChange()) return;

        // Create a new array without the element at the specified index
        const newFormFieldList = formFieldList.filter((_, i) => i !== index);

        // Update the state with the new array
        settingHasChanged.current = true;
        setFormFieldList(newFormFieldList);
    }

    /**
     * Function handle indivisual form field changes
     */
    const handleFormFieldChange = (index, key, value) => {
        if (proSettingChange()) return;
        // copy the form field before modify
        const newFormFieldList = [...formFieldList]

        // Update the new form field list
        newFormFieldList[index] = {
            ...newFormFieldList[index],
            [key]: value
        }

        // Update the state variable
        settingHasChanged.current = true;
        setFormFieldList(newFormFieldList);
    }

    /**
     * Function that handle type change for a particular form field
     * @param {*} index 
     * @param {*} newType
     */
    const handleFormFieldTypeChange = (index, newType) => {
        if (proSettingChange()) return;

        // Get the input which one is selected
        const selectedFormField = formFieldList[index];

        // Check if selected type is previously selected type  
        if (selectedFormField.type == newType) { return }

        // Create a empty form field for that position
        const newFormField = getNewFormField(newType);
        newFormField.id = selectedFormField.id;

        // Replace the newly created form field with old one
        const newFormFieldList = [...formFieldList];
        newFormFieldList[index] = newFormField;

        settingHasChanged.current = true;
        setFormFieldList(newFormFieldList);
    }

    return (
        // Render Registration form here
        <div className="registrationFrom-main-wrapper-section" >
            {/* Render element type section */}
            <Elements
                selectOptions={selectOptions}
                onClick={(type) => {
                    const newInput = appendNewFormField(formFieldList.length - 1, type);
                    setOpendInput(newInput);
                }}
            />

            <div className="registration-form-main-section">
                {/* Render form title here */}
                {
                    <div className="form-heading">
                        <input
                            type="text"
                            placeholder={props.formTitlePlaceholder}
                            value={formFieldList[0]?.label}
                            onChange={(event) => { handleFormFieldChange(0, 'label', event.target.value) }}
                        />
                        <AddNewBtn
                            onAddNew={() => {
                                const newInput = appendNewFormField(0);
                                setOpendInput(newInput);
                            }}
                        />
                    </div>
                }

                {/* Render form fields here */}
                {
                    <ReactSortable
                        list={formFieldList}
                        setList={(newList) => {
                            if (firstTimeRender.current) {
                                firstTimeRender.current = false;
                                return;
                            }
                            if (proSettingChange()) return;
                            settingHasChanged.current = true;
                            setFormFieldList(newList)
                        }}
                        handle=".drag-handle"
                    >
                        {
                            formFieldList.length > 0 &&
                            formFieldList.map((formField, index) => {

                                if (index === 0) { return <div style={{ display: 'none' }}></div> }

                                return (
                                    <main className={`form-field ${opendInput?.id == formField.id ? 'active' : ''}`}>

                                        {/* Render dragable button */}
                                        {
                                            opendInput?.id == formField.id &&
                                            <div className="bth-move drag-handle">
                                                <i className="admin-font font-move"></i>
                                            </div>
                                        }

                                        {/* Render setting section */}
                                        {
                                            opendInput?.id == formField.id &&
                                            <section className="meta-menu">
                                                <div className="btn-delete">
                                                    <DeleteBtn
                                                        onDelete={() => {
                                                            deleteParticularFormField(index);
                                                            setOpendInput(null);
                                                        }}
                                                    />
                                                </div>
                                                <SettingMetaBox
                                                    formField={formField}
                                                    onChange={(key, value) => handleFormFieldChange(index, key, value)}
                                                    onTypeChange={(newType) => handleFormFieldTypeChange(index, newType)}
                                                    inputTypeList={selectOptions}
                                                />
                                            </section>
                                        }

                                        {/* Render main content */}
                                        <section
                                            className={`${opendInput?.id != formField.id ? 'hidden-list' : ''} form-field-container-wrapper`}
                                            onClick={(event) => {
                                                if (opendInput?.id != formField.id) {
                                                    setOpendInput(formField)
                                                }
                                            }}
                                        >

                                            {/* Render question name here */}
                                            {
                                                (
                                                    formField.type == 'text' ||
                                                    formField.type == 'email' ||
                                                    formField.type == 'number'
                                                ) &&
                                                <Template.SimpleInput
                                                    formField={formField}
                                                    onChange={(key, value) => handleFormFieldChange(index, key, value)}
                                                />
                                            }
                                            {
                                                (
                                                    formField.type == 'checkboxes' ||
                                                    formField.type == 'multiselect' ||
                                                    formField.type == 'radio' ||
                                                    formField.type == 'dropdown'
                                                ) &&
                                                <Template.MultipleOptions
                                                    formField={formField}
                                                    selected= {opendInput?.id === formField.id}
                                                    onChange={(key, value) => handleFormFieldChange(index, key, value)}
                                                />
                                            }
                                            {
                                                formField.type == 'textarea' &&
                                                <Template.Textarea
                                                    formField={formField}
                                                    onChange={(key, value) => handleFormFieldChange(index, key, value)}
                                                />
                                            }
                                            {
                                                formField.type == 'attachment' &&
                                                <Template.Attachment
                                                    formField={formField}
                                                    onChange={(key, value) => handleFormFieldChange(index, key, value)}
                                                />
                                            }
                                            {
                                                formField.type == 'recapta' &&
                                                <Template.Recaptach
                                                    formField={formField}
                                                    onChange={(key, value) => handleFormFieldChange(index, key, value)}
                                                />
                                            }
                                            {
                                                formField.type == 'datepicker' &&
                                                <Template.Datepicker
                                                    formField={formField}
                                                    onChange={(key, value) => handleFormFieldChange(index, key, value)}
                                                />
                                            }
                                            {
                                                formField.type == 'timepicker' &&
                                                <Template.Timepicker
                                                    formField={formField}
                                                    onChange={(key, value) => handleFormFieldChange(index, key, value)}
                                                />
                                            }
                                            {
                                                formField.type == 'section' &&
                                                <Template.Section
                                                    formField={formField}
                                                    onChange={(key, value) => handleFormFieldChange(index, key, value)}
                                                />
                                            }
                                            {
                                                formField.type == 'divider' &&
                                                <Template.Divider />
                                            }
                                        </section>

                                        <AddNewBtn
                                            onAddNew={() => {
                                                const newInput = appendNewFormField(index);
                                                setOpendInput(newInput);
                                            }}
                                        />
                                    </main>
                                )
                            })
                        }
                    </ReactSortable>
                }

                <section className="settings-input-content">
                    <ButtonCustomizer
                        text='Submit'
                        setting={buttonSetting}
                        onChange={(key, value) => {
                            if (proSettingChange()) return;
                            settingHasChanged.current = true;
                            const previousSetting = buttonSetting || {};
                            setButtonSetting({ ...previousSetting, [key]: value });
                        }}
                    />
                </section>

                <AddNewBtn
                    learge
                    onAddNew={() => {
                        const newInput = appendNewFormField(formFieldList.length - 1);
                        setOpendInput(newInput);
                    }}
                />
            </div>
        </div>
    );
}

export default CustomFrom;