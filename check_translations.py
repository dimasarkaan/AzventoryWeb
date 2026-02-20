import os
import re

view_dir = 'resources/views'
lang_ui_path = 'lang/id/ui.php'
lang_msg_path = 'lang/id/messages.php'

def extract_keys_from_php_array(filepath):
    keys = set()
    if not os.path.exists(filepath):
        return keys
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    # simplistic regex to find keys in php array
    matches = re.findall(r"['\"]([a-zA-Z0-9_]+)['\"]\s*=>", content)
    for m in matches:
        keys.add(m)
    return keys

ui_keys = extract_keys_from_php_array(lang_ui_path)
msg_keys = extract_keys_from_php_array(lang_msg_path)

used_ui_keys = set()
used_msg_keys = set()

# simplistic regex for translation functions: __('ui.key') or @lang('ui.key')
pattern = re.compile(r"(?:__|trans|@lang)\(\s*['\"](?:(ui|messages)\.)([a-zA-Z0-9_]+)['\"]")

for root, _, files in os.walk(view_dir):
    for file in files:
        if file.endswith('.blade.php') or file.endswith('.js'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            matches = pattern.findall(content)
            for file_type, key in matches:
                if file_type == 'ui':
                    used_ui_keys.add(key)
                elif file_type == 'messages':
                    used_msg_keys.add(key)

# Check missing
missing_ui = used_ui_keys - ui_keys
missing_msg = used_msg_keys - msg_keys

print(f"Missing UI keys: {missing_ui}")
print(f"Missing MSG keys: {missing_msg}")

