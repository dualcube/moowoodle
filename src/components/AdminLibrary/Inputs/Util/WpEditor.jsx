import { Editor } from '@tinymce/tinymce-react';

const WpEditor = (props) => {
    return (
        <>
            <Editor
                apiKey={props.apiKey}
                value={value}
                init={{
                    height: 200,
                    plugins: 'media',
                }}
                onEditorChange={(e) => { props.ontinyChange(e) }}
            />
        </>
    );
}

export default WpEditor;