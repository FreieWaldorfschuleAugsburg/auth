type Dictionary = { [index: string]: string }

declare global {
    interface Window {
        _translations: {
            [key: string]: string | object
        };
    }
}

function hasOwnProperty<O extends object, K extends PropertyKey>(
    obj: O,
    key: K
): obj is O & Record<K, O> {
    return Object.prototype.hasOwnProperty.call(obj, key);
}

type Replacements = {
    [key: string]: string
}

//This looks like a rather magic function for parsing
export function useTranslation(key: string, replacements: Replacements = {}) {
    const keyLevels = key.split(".").slice(1, key.length);
    let translation = window._translations[key.split(".")[0]];
    keyLevels.forEach(level => {
        if (typeof translation === "object" && hasOwnProperty(translation, level)) {
            translation = translation[level];
        }
    });
    Object.keys(replacements).forEach(replacement => {
        if (typeof translation === "string") {
            translation = translation.replace(`:${replacement}`, replacements[replacement]);
        }
    });

    return translation as string
}
