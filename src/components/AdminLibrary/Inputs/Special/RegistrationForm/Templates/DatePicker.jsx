import { useState, useEffect } from 'react';

const Datepicker = (props) => {
    const { formField, onChange } = props;

    return (
        <>
            <div className='main-input-wrapper'>
                {/* Render label */}
                <input
                    className='input-label textArea-label'
                    type="text"
                    value={formField.label}
                    placeholder={"I am label"}
                    onChange={(event) => {
                        onChange('label', event.target.value);
                    }}
                />

                {/* Render attachments */}
                <input type="date" readOnly />
            </div>
        </>
    )
}

export default Datepicker;