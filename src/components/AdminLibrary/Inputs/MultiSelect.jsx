const MultiSelect = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                {m.select_deselect ? (
                    <div
                        className="select-deselect-trigger"
                        onClick={(e) => {
                            this.onMultiSelectDeselectChange(e, m);
                        }}
                    >
                    {appLocalizer.global_string.select_deselect_all}
                    </div>
                ) : (
                    ''
                )}
                <Select
                    className={props.inputClass}
                    value={props.value}
                    options={optionsData}
                    onChange={(e) => { props.onChange?.(e, optionsData) }}
                ></Select>
                {
                    props.description &&
                    <p className={props.descClass} dangerouslySetInnerHTML={{__html: props.description}}>
                    </p>
                }
            </div>
        </>
    );
}

export default MultiSelect;