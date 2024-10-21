const DefaultMeta = (props) => {
    const { defaultvalue, name, deactive, onChange } = props;
    const { hideDefaultValue, hideName, hideDeactive } = props;
    
    return (
        <>
            {
                !hideDeactive &&
                <div className="deactive">
                    <span>Deactive</span> 
                    <input
                        type="checkbox"
                        checked={deactive}
                        onChange={(event) => { onChange( 'deactive', ! event.target.checked ) }}
                    />
                </div>
            }
            {
                !hideName &&
                <div className="name">
                    <span>Set name</span> 
                    <input
                        type="text"
                        value={name}
                        onChange={(event) => { onChange( 'name', ! event.target.name ) }}
                    />
                </div>
            }
            {
                !hideDefaultValue &&
                <div className="default-value">
                    <span>Set Defaut Value</span>
                    <input
                        type="text"
                        value={defaultvalue}
                        onChange={(event) => { onChange( 'defaultvalue', ! event.target.name ) }}
                    />
                </div>
            }
        </>
    );
}

export default DefaultMeta;