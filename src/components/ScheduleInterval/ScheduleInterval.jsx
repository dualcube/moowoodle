import React from 'react';
import './ScheduleInterval.scss';

const ScheduleInterval = ( props ) => {
    const optionsData = [];
    let defaultValue = {};

    props.options.forEach((option, index) => {
        optionsData[index] = {
            value: option.value,
            label: option.label,
            index,
        };

        if ( option.value === props.value ) {
            defaultValue = optionsData[index];
        }
    });

  return (
    <>

    <div className='radio-buttons-container'>
        {optionsData.map((item, index)=>{
            return(
                <>
                    <div class="radio-button">
                        <input
                            checked = { item.value === defaultValue.value }
                            onChange={(e) => {props.onChange(item)}}
                            name="radio-group"
                            id={index} class="radio-button__input"
                            type="radio"
                        />
                        <label htmlFor={index} class="radio-button__label">
                            <span class="radio-button__custom"></span>
                                {item.label}
                        </label>
                    </div>
                </>
            )
        })}
        {
            props.proSetting && <span className="admin-pro-tag">pro</span>
        }
    </div>
    </>
  )
}

export default ScheduleInterval