import Select from 'react-select';

const SelectInput = (props) => {
    const optionsData = [];
    let defaulValue = '';

    props.options.forEach((option, index) => {
        optionsData[index] = {
            value: option.value,
            label: option.label,
            index,
        };

        if ( option.value === props.value ) {
            defaulValue = optionsData[index];
        }
    });
    return (
        <>
            <div className={props.wrapperClass}>
                {
                    props.selectDeselect &&
                    <>
                        <button
                            className={props.selectDeselectClass}
                            onClick={(e) => { 
                                e.preventDefault();
                                props.onMultiSelectDeselectChange?.(e) 
                            }}
                        >
                            {props.selectDeselectValue}
                        </button>
                    </>
                }
                <Select
                    className={props.inputClass}
                    value={defaulValue || props.value}
                    options={optionsData}
                    onChange={(e) => { props.onChange?.(e) }}
                    onClick= {(e) => { props.onClick?.(e) }}
                    isMulti={props.type === 'multi-select'}
                ></Select>
                 {
                    props.proSetting && <span className="admin-pro-tag">pro</span>
                }
                {
                    props.description &&
                    <p className={props.descClass} dangerouslySetInnerHTML={{ __html: props.description }} >
                    </p>
                }
			</div>
        </>
    );
}

export default SelectInput;