import { useRef, useState } from "react";

const SSOKey = (props) => {
    const { value, proSetting, onChange } = props;

    const inputRef = useRef();
    const [ copied, setCopied ] = useState( false );

    function generateRandomKey( length = 8 ) {
        const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        let key = "";
        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * characters.length);
            key += characters.charAt(randomIndex);
        }
        return key;
    }

    const generateSSOKey = (e) => {
        e.preventDefault();
        const key = generateRandomKey(8);
        onChange( key );
    }

    const handleCopy = (e) => {
        e.preventDefault();
        navigator.clipboard.writeText(value)
            .then(() => {
                setCopied( true );
            });
    }

    return (
        <div>
            <input
                type="text"
                value={value}
                onChange={ (e) => onChange( e.target.value ) }
            />
            <button
                onClick={ handleCopy }
            >{ copied ? ' ✔copied' : 'copy' }</button>
            <button
                onClick={ generateSSOKey }
            >Generate</button>
        </div>
    );
}

export default SSOKey;