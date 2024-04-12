import Select from 'react-select';

const SelectInput = (props) => {
    const optionsData = [];
    props.options.forEach((option, index) => {
        optionsData[index] = {
            value: option.value,
            label: option.label,
            index,
        };
    });
    return (
        <>
            <div className={props.wrapperClass}>
                {
                    props.selectDeselect &&
                    <>
                        <div
                            className={props.selectDeselectClass}
                            onClick={(e) => { props.onMultiSelectDeselectChange?.(e) }}
                        >
                            {props.selectDeselectValue}
                        </div>
                    </>
                }
                <Select
                    className={props.inputClass}
                    value={props.value}
                    options={optionsData}
                    onChange={(e) => { props.onChange?.(e) }}
                    onClick= {(e) => { props.onClick?.(e) }}
                    isMulti={props.type === 'multi-select'}
                ></Select>
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