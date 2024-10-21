import { useState, useEffect } from 'react';
import Recapcha from '../../../../../../assets/images/recaptcha.png';

const Recaptach = (props) => {
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
                <img className='recapcha-img' src={Recapcha} alt="Recapcha" />
            </div>
        </>
    )
}

export default Recaptach;