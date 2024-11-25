import { Editor } from '@tinymce/tinymce-react';

const WpEditor = (props) => {
    return (
        <>
            <Editor
                apiKey={props.apiKey}
                value={props.value}
                init={{
                    height: 200,
                    plugins: 'media',
                }}
                onEditorChange={(e) => { props.onEditorChange(e) }}
            />
        </>
    );
}

export default WpEditor;