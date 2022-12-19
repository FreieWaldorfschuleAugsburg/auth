import {InertiaForm} from "@inertiajs/inertia-vue3";
import {watch} from "vue";
import {Inertia, Method} from "@inertiajs/inertia";


type LoginForm = {
    [key: string]: string
}


export function usePrevalidate(form: InertiaForm<LoginForm>, {
    method,
    url
}: { method: Method, url: string }) {
    const touchedFields = new Set<string>;
    let needsValidation = false;

    watch(() => form.data(), (newData, oldData) => {
        let changedFields = Object.keys(newData).filter((field) => newData[field] !== oldData[field])
        changedFields.forEach(changedField => {
            touchedFields.add(changedField);
        })
        //
        needsValidation = true;
    })


    function validate() {
        Inertia.visit(url, {
            method,
            data: {
                ...form.data(),
                prevalidate: true
            },
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => form.clearErrors(),
            onError: (errors) => {
                Object.keys(errors)
                    .filter(field => !touchedFields.has(field))
                    .forEach(field => delete errors[field])

                form.clearErrors().setError(errors)
            }
        })

    }

    return {validate}
}
