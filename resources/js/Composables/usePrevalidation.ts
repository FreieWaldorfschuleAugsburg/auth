import {watch} from "vue";
import { InertiaForm, router } from "@inertiajs/vue3";


type LoginForm = {
    samaccountname: string | null
    password: string | null
}

type Method = "get" | "post"


export function usePrevalidate(form: InertiaForm<LoginForm>, {
    method,
    url
}: { method: Method, url: string }) {
    const touchedFields = new Set<string>;
    let needsValidation = false;

    watch(() => form.data(), (newData, oldData) => {
        // @ts-ignore
        let changedFields = Object.keys(newData).filter((field) => newData[field] !== oldData[field])
        changedFields.forEach(changedField => {
            touchedFields.add(changedField);
        })
        //
        needsValidation = true;
    })


    function validate() {
        router.visit(url, {
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

                // @ts-ignore
                form.clearErrors().setError(errors)
            }
        })

    }

    return {validate}
}
