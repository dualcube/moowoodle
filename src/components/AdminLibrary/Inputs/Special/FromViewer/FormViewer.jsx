import { useState, useEffect } from 'react';
import Select from 'react-select';
import Button from '../DisplayButton';
import './FromViewer.scss';
import { __ } from '@wordpress/i18n';

/**
 * Render checkboxes
 * @param {*} props 
 * @returns 
 */
const Checkboxes = (props) => {
    const { options, onChange } = props;

    const [checkedItems, setCheckedItems] = useState(options.filter(({ isdefault }) => isdefault));

    useEffect(() => {
        onChange(checkedItems.map((item) => item.value));
    }, [checkedItems])

    const handleChange = (option, checked) => {
        const newCheckedItems = checkedItems.filter((item) => item.value != option.value);

        if (checked) {
            newCheckedItems.push(option);
        }

        setCheckedItems(newCheckedItems);
    }

    return (
        <div className='multiselect-container items-wrapper'>
            {
                options.map((option, index) => {
                    return (
                        <div key={option} className='select-items'>
                            <input
                                type="checkbox"
                                id={option.value}
                                checked={checkedItems.find((item) => item.value === option.value)}
                                onChange={(e) => handleChange(option, e.target.checked)}
                            />
                            <label htmlFor={option.value}>
                                {option.label}
                            </label>
                        </div>
                    );
                })
            }
        </div>
    );
}

/**
 * Render Multiselect
 * @param {*} props 
 * @returns 
 */
const Multiselect = (props) => {
    const { options=[], onChange, isMulti } = props;

    const [selectedOptions, setSelectedOptions] = useState(() => {
        if (isMulti) {
            return options.filter(({ isdefault }) => isdefault);
        } else {
            return options.find(({ isdefault }) => isdefault);
        }
    });

    useEffect(() => {
        if (isMulti) {
            onChange(selectedOptions.length > 0 ? selectedOptions.map(option => option.value) : []);
        } else {
            onChange(selectedOptions ? selectedOptions.value : null);
        }
    }, [selectedOptions])

    const handleChange = (selectedOptions) => {
        setSelectedOptions(selectedOptions || (isMulti ? [] : null));
    };

    return (
        <Select
            isMulti={isMulti}
            value={selectedOptions}
            onChange={handleChange}
            options={options}
        />
    );
}

/**
 * Render radio
 * @param {*} props 
 */
const Radio = (props) => {
    const { options, onChange } = props;

    const [selectdedItem, setSelectdedItem] = useState(options.find(({ isdefault }) => isdefault)?.value);

    useEffect(() => {
        onChange(selectdedItem);
    }, [selectdedItem])

    const handleChange = (e) => {
        setSelectdedItem(e.target.value);
    }

    return (
        <div className='multiselect-container items-wrapper'>
            {
                options.map((option, index) => {
                    return (
                        <div key={index} className='select-items'>
                            <input
                                type="radio"
                                id={option.value}
                                value={option.value}
                                checked={selectdedItem === option.value}
                                onChange={handleChange}
                            />
                            <label htmlFor={option.value}>
                                {option.label}
                            </label>
                        </div>
                    );
                })
            }
        </div>
    );
}

/**
 * Pro form components
 * @param {*} props 
 * @returns 
 */
const FromViewer = (props) => {

    const { formFields, onSubmit } = props;

    const [inputs, setInputs] = useState({});

    // Get the from list and button settings
    const formList = formFields.formfieldlist || [];
    const buttonSetting = formFields.butttonsetting || {}
    const [captchaToken, setCaptchaToken] = useState(null);
    const [captchaError, setCaptchaError] = useState(false);
    const [fileName, setFileName] = useState("");
    const [file, setFile] = useState(null); 

    const recaptchaField = formList.find((field) => field.type === "recaptcha");
    const siteKey = recaptchaField ? recaptchaField.sitekey : null;

    useEffect(() => {  
        const loadRecaptcha = () => {
            window.grecaptcha.ready(() => {
                window.grecaptcha.execute(siteKey, { action: "form_submission" })
                    .then((token) => {
                        setCaptchaToken(token);
                    })
                    .catch((error) => {
                        setCaptchaError(true);
                    });
            });
        };
    
        if (!window.grecaptcha) {
            const script = document.createElement("script");
            script.src = `https://www.google.com/recaptcha/api.js?render=${siteKey}`;
            script.async = true;
            script.onload = loadRecaptcha;
            script.onerror = () => {
                setCaptchaError(false);
            };
            document.body.appendChild(script);
        } else {
            loadRecaptcha();
        }
    }, [siteKey]);
    

    /**
     * Handle input change
     * @param {*} e 
     */
    const handleChange = (name, value) => {
        setInputs((prevData) => ({
            ...prevData,
            [name]: value,
        }));
    };

    const handleFileChange = (name, event) => {
        const selectedFile = event.target.files[0];
        if (selectedFile) {
            setFileName(selectedFile.name);
            setFile(selectedFile);
            setInputs((prevData) => ({
                ...prevData,
                [name]: selectedFile,
            }));
        }
    }
    /**
     * Handle input submit
     * @param {*} e 
     */
    const handleSubmit = async (e) => {
        e.preventDefault();

        const data = new FormData();

        for (const key in inputs) {
            if (inputs.hasOwnProperty(key)) {
                data.append(key, inputs[key]);
            }
        }

        onSubmit(data);
    }

    const [ defaultDate, setDefaultDate ] = useState(new Date().getFullYear()+'-01-01')

    return (
        <main className='catalogx-enquiry-pro-form'>
            {
                formList.map((field) => {
                    if (field.disabled) { return }

                    switch (field.type) {
                        case "title":
                            return (
                                <section className="form-title"> {field.label} </section>
                            );
                        case "text":
                            return (
                                <section className='form-text form-pro-sections'>
                                    <label>{field.label}</label>
                                    <input
                                        type="text"
                                        name={field.name}
                                        value={
                                            field.name === 'name' 
                                                ? (enquiryFormData.default_placeholder.name || inputs[field.name]) 
                                                : inputs[field.name]
                                        }
                                        placeholder={field.placeholder}
                                        onChange={(e) => handleChange(field.name, e.target.value)}
                                        required={field.required}
                                        maxLength={field.charlimit}
                                    />
                                </section>
                            );
                        case "email":
                            return (
                                <section className='form-email form-pro-sections'>
                                    <label>{field.label}</label>
                                    <input
                                        type="email"
                                        name={field.name}
                                        value={enquiryFormData.default_placeholder.email || inputs[field.key]}
                                        placeholder={field.placeholder}
                                        onChange={(e) => handleChange(field.name, e.target.value)}
                                        required={field.required}
                                        maxLength={field.charlimit}
                                    />
                                </section>
                            );
                        case "textarea":
                            return (
                                <section className=' form-pro-sections'>
                                    <label>{field.label}</label>
                                    <textarea
                                        name={field.name}
                                        value={inputs[field.name]}
                                        placeholder={field.placeholder}
                                        onChange={(e) => handleChange(field.name, e.target.value)}
                                        required={field.required}
                                        maxLength={field.charlimit}
                                        rows={field.row}
                                        cols={field.col}
                                    />
                                </section>
                            );
                        case "checkboxes":
                            return (
                                <section className=' form-pro-sections'>
                                    <label>{field.label}</label>
                                    <Checkboxes
                                        options={field.options}
                                        onChange={(data) => handleChange(field.name, data)}
                                    />
                                </section>
                            );
                        case "multiselect":
                            return (
                                <section className=' form-pro-sections'>
                                    <label>{field.label}</label>
                                    <div className='multiselect-container'>
                                        <Multiselect
                                            options={field.options}
                                            onChange={(data) => handleChange(field.name, data)}
                                            isMulti
                                        />
                                    </div>
                                </section>
                            );
                        case "dropdown":
                            return (
                                <section className=' form-pro-sections'>
                                    <label>{field.label}</label>
                                    <div className='multiselect-container'>
                                        <Multiselect
                                            options={field.options}
                                            onChange={(data) => handleChange(field.name, data)}
                                        />
                                    </div>
                                </section>
                            );
                        case "radio":
                            return (
                                <section className=' form-pro-sections'>
                                    <label>{field.label}</label>
                                    <Radio
                                        options={field.options}
                                        onChange={(data) => handleChange(field.name, data)}
                                    />
                                </section>
                            );
                        case "recaptcha":
                            return (
                                <section className=' form-pro-sections'>
                                    <div className='recaptcha-wrapper'>
                                        <input type="hidden" name="g-recaptcha-response" value={captchaToken} />
                                    </div>
                                </section>
                            );
                        case "attachment":
                            return (
                                <section className='form-pro-sections'>
                                    <label>{field.label}</label>
                                    <div className="attachment-section">
                                        <label
                                            htmlFor="dropzone-file"
                                            className="attachment-label"
                                        >
                                            <div className="wrapper">
                                                <i class="adminLib-cloud-upload"></i>
                                                <p className="heading">
                                                    {fileName == '' ? (
                                                        <>
                                                            <span>{ __('Click to upload', 'catalogx') }</span> { __('or drag and drop', 'catalogx') }
                                                        </>
                                                    ) : fileName}
                                                </p>
                                            </div>
                                            <input readOnly id="dropzone-file" type="file" className="hidden" 
                                             onChange={(e) => handleFileChange(field.name, e)} // Handle file input change
                                             />
                                        </label>
                                    </div>
                                </section>
                            );
                        case "datepicker":
                            return (
                                <section className=' form-pro-sections'>
                                    <label>{field.label}</label>
                                    <div className='date-picker-wrapper'>
                                        <input
                                            type='date'
                                            value={inputs[field.name] || defaultDate}
                                            onChange={(e) => { handleChange(field.name, e.target.value) }}
                                        />
                                    </div>
                                </section>
                            );
                        case "timepicker":
                            return (
                                <section className=' form-pro-sections'>
                                    <label>{field.label}</label>
                                    <input
                                        type='time'
                                        value={inputs[field.name]}
                                        onChange={(e) => { handleChange(field.name, e.target.value) }}
                                    />
                                </section>
                            );
                        case "section":
                            return (
                                <section className=' form-pro-sections'>
                                    {field.label}
                                </section>
                            );
                        case "divider":
                            return(
                                <section className='section-divider-container'></section>
                            )
                    }
                })
            }

            <section className='popup-footer-section'>
                <Button
                    customStyle={buttonSetting}
                    onClick={(e) => {
                        const captcha = formList.find((field) => field.type === "recaptcha");
                        if (captcha?.disabled === false) {
                            if (captchaError) {
                                return;
                            }
                            if (!captchaToken) {
                                return;
                            }
                        } 
                        handleSubmit(e)
                    }}
                    children={'Submit'}
                />
                <button id='catalogx-close-enquiry-popup' className='catalogx-close-enquiry-popup'>{__('Close', 'catalogx')}</button>
            </section>

        </main>
    );
}

export default FromViewer;