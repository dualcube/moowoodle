import React, { useState, useEffect, useRef } from 'react';
import './FormCustomizer.scss'
import '../RegistrationForm/RegistrationForm.scss'
import SubTabSection from '../SubTabSection/SubTabSection';
import ProForm from '../RegistrationForm/RegistrationForm';
import { __ } from "@wordpress/i18n";

const FormCustomizer = (props) => {
    const {setting, proSetting, proSettingChange, moduleEnabledChange, onChange} = props;
    const settingChange = useRef(false);
    const [formFieldsData, setFromFieldsData] = useState(setting['freefromsetting'] || []);
    
    useEffect(() => {
        if (settingChange.current) {
            onChange('freefromsetting', formFieldsData);
            settingChange.current = false;
        }
    }, [formFieldsData]);
    
    const getFields = (fieldKey) => {
        return formFieldsData.find(({ key }) => { return key === fieldKey });
    }
    
    const activeDeactiveFields = (fieldKey, activeStatus) => {
        if (moduleEnabledChange()) return;
        settingChange.current = true;
        if (getFields(fieldKey)) {
            setFromFieldsData((prevData) => {
                return prevData.map((data) => {
                    if (data.key === fieldKey) {
                        return { ...data, active: activeStatus }
                    }
                    return data;
                })
            });
        } else {
            setFromFieldsData((prevData) => {
                return [...prevData, { key: fieldKey, label: '', active: activeStatus }]
            });
        }
    }
    
    const updateFieldLabel = (fieldKey, labelValue) => {
        if (moduleEnabledChange()) return;
        settingChange.current = true;
        if (getFields(fieldKey)) {
            setFromFieldsData((prevData) => {
                return prevData.map((data) => {
                    if (data.key === fieldKey) {
                        return { ...data, label: labelValue }
                    }
                    return data;
                })
            });
        } else {
            setFromFieldsData((prevData) => {
                return [...prevData, { key: fieldKey, label: labelValue, active: false }]
            });
        }
    }
    
    const formFields = [
        {
            key: 'name',
            desc: 'Name'
        },
        {
            key: 'email',
            desc: 'Email'
        },
        {
            key: 'phone',
            desc: 'Phone'
        },
        {
            key: 'address',
            desc: 'Address'
        },
        {
            key: 'subject',
            desc: 'Enquiry about'
        },
        {
            key: 'comment',
            desc: 'Enquiry details'
        },
        {
            key: 'fileupload',
            desc: 'File upload',
        },
        {
            key: 'filesize-limit',
            desc: 'File upload size limit (in MB)',
        },
        {
            key: 'captcha',
            desc: 'Captcha',
        }
    ]

    const [menu, setMenu] = useState([
        { name: "Free", link: "hi", id: 2, icon: 'adminLib-info' },
        { name: "Pro", link: "hi", id: 1, icon: 'adminLib-cart' },
    ]);

    // Set default current tab
    const [currentTab, setCurrentTab] = useState(menu[0]);
    const [readonlyFields, setReadonlyFields] = useState(formFields.map((field, index) => formFieldsData[index]?.active==true ? false : true));

    return (
        <>
            <SubTabSection menuitem={menu} currentTab={currentTab} setCurrentTab={setCurrentTab} />
            {
                currentTab.id == 1 ?
                    <ProForm
                        name='formsettings'
                        proSettingChange={proSettingChange}
                        onChange={(value) => onChange('formsettings', value)}
                    />
                    :
                    <div>
                        <div className='fields-header'>
                        <h3 className="name">{ __('Field Name', 'catalogx') }</h3>
                        <h3 className="set-name">{ __('Set new field name', 'catalogx') }</h3>
                        </div>
                        <div className='registrationFrom-main-wrapper-section'>
                            <div className='form-field'>
                                {
                                    formFields.map((fields, index) => {
                                        return (
                                            <div className='edit-form-wrapper free-form' key={index}>
                                                <div className='form-label' style={{ opacity: readonlyFields[index] ? "0.3" : "1" }} >{fields.desc}</div>
                                                <div className='settings-form-group-radio' >
                                                    <input
                                                        type='text'
                                                        onChange={(e) => {
                                                            updateFieldLabel(fields.key, e.target.value);
                                                        }}
                                                        value={getFields(fields.key) ? getFields(fields.key).label : ''}
                                                        readOnly={readonlyFields[index]}
                                                        style={{ opacity: readonlyFields[index] ? "0.3" : "1" }}
                                                        
                                                    />
                                                </div>
                                                <div
                                                    class="button-visibility"
                                                    onClick={() => {
                                                        setReadonlyFields(prev =>
                                                            prev.map((readonly, i) => i === index ? !readonly : readonly)                                                            
                                                        );
                                                        activeDeactiveFields(fields.key, readonlyFields[index]);
                                                    }}
                                                >
                                                    <i class={`admin-font ${readonlyFields[index] ? 'adminLib-eye-blocked enable-visibility' : 'adminLib-eye'}`}></i>
                                                </div>
                                            </div>
                                        );
                                    })
                                }
                            </div>
                        </div>
                    </div>

            }

        </>
    );
}
export default FormCustomizer;