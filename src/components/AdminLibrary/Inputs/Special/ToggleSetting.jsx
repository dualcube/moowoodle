import React from 'react';
import './ToggleSetting.scss';

const ToggleSetting = (props) => {

    const { description, key, options, wrapperClass, descClass, value, onChange, proSetting } = props;

    return (
        <>
            <section className={wrapperClass}>
                <div className='toggle-setting-container'>
                    <ul>
                        {options?.map((option, index) => {
                        const checked = value == option.value;

                            return (
                                <>
                                    <li key={index}  onClick={() => onChange(option.value)}>
                                        <input class="toggle-setting-form-input" type="radio" id={option.key} name="approve_vendor" value={value} checked={checked} />
                                        <label htmlFor={option.value}>{option.label}</label>
                                    </li>
                                </>
                            )
                        })}
                    </ul>
                </div>
                { proSetting && <span className="admin-pro-tag">pro</span> }
                {description && 
                    <p className={descClass} dangerouslySetInnerHTML={{__html: description}}></p>
                }
            </section>
        </>
    )
}

export default ToggleSetting