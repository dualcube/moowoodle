import os
import re

# Define the conversion factors
px_to_rem = 0.063
em_to_rem = 0.047

def convert_px_to_rem(match):
    px_value = float(match.group(1))
    rem_value = round(px_value * px_to_rem, 3)
    return f'{rem_value}rem'

def convert_em_to_rem(match):
    em_value = float(match.group(1))
    rem_value = round(em_value * em_to_rem, 3)
    return f'{rem_value}rem'

def convert_css_to_rem(css_file):
    with open(css_file, 'r+') as f:
        css_content = f.read()

        # Regular expressions to match pixel values and em values
        px_pattern = r'(\d+\.?\d*)px'
        em_pattern = r'(\d+\.?\d*)em'

        # Replace pixel values with rem values
        rem_content = re.sub(px_pattern, convert_px_to_rem, css_content)
        # Replace em values with rem values
        rem_content = re.sub(em_pattern, convert_em_to_rem, rem_content)

        # Move pointer to the beginning of the file and write the modified content
        f.seek(0)
        f.write(rem_content)
        f.truncate()

def convert_all_css_to_rem():
    folder_path = '.'  # Current directory
    for filename in os.listdir(folder_path):
        if filename.endswith('.css') or filename.endswith('.scss') or filename.endswith('.min.css'):
            css_file = os.path.join(folder_path, filename)
            convert_css_to_rem(css_file)

def main():
    convert_all_css_to_rem()
    print('Conversion complete. Original files modified.')

if __name__ == "__main__":
    main()

