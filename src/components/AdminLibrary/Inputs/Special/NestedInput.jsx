import BasicInput from '../BasicInput.jsx';
import CheckBox from '../CheckBox.jsx';
import SelectInput from '../SelectInput.jsx';

const NestedInput = (props) => {
    return (
        <>
            <div className={props.mainWrapperClass}>
                {
                    props.databaseValue &&
                    props.databaseValue.map((option, index) =>
                        <div className={props.ParentWrapperClass}>
                            {
                                props.parentOptions.map((parentOption) =>
                                    parentOption.depend &&
                                        option[parentOption.depend] &&
                                        option[parentOption.depend].value &&
                                        option[parentOption.depend].value != parentOption.dependvalue
                                        ? '' :
                                        <div className={props.innerParentWrapperClass}>
                                            <label className={props.parentLabelClass}>
                                                <p>{parentOption.label}</p>
                                            </label>
                                            {
                                                parentOption.type === 'text' ||
                                                parentOption.type === 'number' &&
                                                <BasicInput
                                                    inputClass={props.parentInputClass}
                                                    type={parentOption.type}
                                                    value={option[parentOption.key]}
                                                    onChange={(e) => props.parentOnchage(e)}
                                                />
                                            }
                                            {
                                                parentOption.type === 'checkbox' &&
                                                <CheckBox
                                                    inputClass={props.parentInputClass}
                                                    type='text'
                                                    value='true'
                                                    checked={option[parentOption.key]}
                                                    onChange={(e) => props.parentOnchage(e)}
                                                />
                                            }
                                            {
                                                parentOption.type === 'select' ||
                                                parentOption.type === 'select2nd' ||
                                                parentOption.type === 'country' &&
                                                <SelectInput
                                                    inputClass={props.parentInputClass}
                                                    value={option[parentOption.key]}
                                                    option={parentOption.options}
                                                    onChange={(e, option) => props.parentOnchage(e, option)}
                                                />
                                            }
                                        </div>
                                )
                            }
                        </div>
                    )
                }
            </div>
        </>
    );
}

export default NestedInput;