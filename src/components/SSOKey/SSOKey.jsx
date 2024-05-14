const SSOKey = (props) => {
    const { value, proSetting, onChange } = props;

    function generateRandomKey( length = 8 ) {
        const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        let key = "";
        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * characters.length);
            key += characters.charAt(randomIndex);
        }
        return key;
    }

    const generateSSOKey = ( e ) => {
        
    } 

    return (
        <div>
            <input
                type="text"
                value={value}
                onChange={ (e) => onChange( e.target.value ) }
            />
            <button>coppy</button>
            <button
                onClick={ generateSSOKey() }
            >Generate</button>
        </div>
    );
}

export default SSOKey;