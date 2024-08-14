const CheckBox = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                <input
                    className=  {props.inputClass}
                    id=         {props.id}
                    key=        {props.key}
                    type=       {props.type || 'checkbox'}
                    name=       {props.name || 'basic-input'}
                    value=      {props.value}
                    checked=    {props.checked}
                    onChange=   {(e) => { props.onChange?.(e) }}
                    onClick=    {(e) => { props.onClick?.(e) }}
                    onMouseOver={(e) => { props.onMouseOver?.(e) }}
                    onMouseOut= {(e) => { props.onMouseOut?.(e) }}
                />
                {
                    props.label &&
                    <label htmlFor={`admin-toggle-switch-${props.label}`} ></label>
                }
                 {
                    props.proSetting && <span className="admin-pro-tag">pro</span>
                }
            </div>
                {
                    props.description &&
                    <p className={props.descClass} 
                       dangerouslySetInnerHTML={{ __html: props.description }}>
                    </p>
                }
        </>
    );
}

export default CheckBox;