import React, { useState, useEffect, useRef } from 'react';
import './MergeComponent.scss';

const MergeComponent = (props) => {
    const {wrapperClass, descClass, description, onChange, value, proSetting, fields = []} = props;
    const firstRender = useRef(true);

    // Initialize state based on field names dynamically
    const initialState = fields.reduce((acc, field) => {
        acc[field.name] = value[field.name] || '';
        return acc;
    }, {});
    const [data, setData] = useState(initialState);

    const handleOnChange = (key, value) => {
        setData((previousData) => ({ ...previousData, [key]: value }));
    };

    useEffect(() => {
        if (firstRender.current) {
            firstRender.current = false;
            return; // Prevent the initial call
        }
        onChange(data);
    }, [data]);

    return (
        <main className={wrapperClass}>
            <section className='select-input-section merge-components'>
                {fields.map((field, index) => {
                    const { name, type, options = [], placeholder = "Enter a value" } = field;

                    // Dynamically render field based on type
                    if (type === 'select') {
                        return (
                            <select key={index} id={name} value={data[name]} onChange={(e) => handleOnChange(name, e.target.value)}>
                                <option value="">Select</option>
                                {options.map((option) => (
                                    <option key={option.value} value={option.value}>{option.label}</option>
                                ))}
                            </select>
                        );
                    } else if (type === 'number') {
                        return (
                            <input
                                key={index}
                                type={type}
                                id={name}
                                placeholder={placeholder}
                                value={data[name]}
                                min="1"
                                onChange={(e) => handleOnChange(name, e.target.value)}
                            />
                        );
                    }

                    return null; // Return null if type is not recognized
                })}
            </section>
            {description && <p className={descClass} dangerouslySetInnerHTML={{ __html: description }} />}
            {proSetting && <span className="admin-pro-tag">pro</span>}
        </main>
    );
};

export default MergeComponent;
