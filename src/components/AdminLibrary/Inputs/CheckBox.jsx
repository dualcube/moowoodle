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
                    <label htmlFor={`woo-toggle-switch-${props.label}`} ></label>
                }
                {
                    props.pro && 
                    <span className="table-content-pro-tag stock-manager-pro-tag">Pro</span>
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