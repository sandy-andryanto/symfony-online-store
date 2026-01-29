import axios from "axios"

const httpWithToken = (auth_token) => {

    let headers = {
        "Content-type": "application/json"
    }

    if (localStorage.getItem('auth_token')) {
        auth_token = localStorage.getItem('auth_token')
    }

    if (auth_token !== undefined && auth_token !== null) {
        headers = {
            ...headers,
            "Authorization ": `Bearer ${auth_token}`
        }
    }

    return axios.create({ baseURL: `${import.meta.env.VITE_APP_BACKEND_URL}`, headers: headers })
}

const httpService = (auth) => {

    let headers = {
        "Content-type": "application/json"
    }

    if (auth) {
        const auth_token = localStorage.getItem('auth_token')
        headers = {
            ...headers,
            "Authorization ": `Bearer ${auth_token}`
        }
    }

    return axios.create({ baseURL: `${import.meta.env.VITE_APP_BACKEND_URL}`, headers: headers })
}

const ping = async () => {
    return await httpService(false).get("/api/home/ping")
}

const expiredMessage = `Your session has been expired. Please log in again to continue using the app`

const getFile = (param) => {
    return `${import.meta.env.VITE_APP_BACKEND_URL}/${param}`
}

const auth = {
    login: async (body) => {
        const post = { username: body.email, password: body.password }
        return await httpService(false).post("/api/auth/login_check", post)
    },
    register: async (body) => {
        const post = { 
            name: body.name, 
            email: body.email,
            password: body.password,
            passwordConfirm: body.password_confirmation
        }
        return await httpService(false).post("/api/auth/register", post)
    },
    confirm: async (token) => {
        return await httpService(false).get(`/api/auth/confirm/${token}`)
    },
    forgot: async (body) => {
        return await httpService(false).post("/api/auth/email/forgot", body)
    },
    reset: async (token, body) => {
        const post = { 
            email: body.email,
            password: body.password,
            passwordConfirm: body.password_confirmation
        }
        return await httpService(false).post(`/api/auth/email/reset/${token}`, post)
    },
}

const profile = {
    detail: async () => {
        return await httpWithToken().get("/api/profile/detail")
    },
    activity: async () => {
        return await httpService(true).get("/api/profile/activity")
    },
    changePassword: async (body) => {
         const post = { 
            curentPassword: body.current_password,
            password: body.password,
            passwordConfirm: body.password_confirmation
        }
        return await httpService(true).post("/api/profile/password", post)
    },
    changeProfile: async (body) => {
        return await httpService(true).post("/api/profile/update", body)
    },
    upload: async (file) => {

        const auth_token = localStorage.getItem('auth_token')
        const formData = new FormData();

        formData.append('file_image', file);

        let headerUpload = {
            'Content-Type': 'multipart/form-data',
            "Authorization ": `Bearer ${auth_token}`
        }

        return await axios.create({ baseURL: `${import.meta.env.VITE_APP_BACKEND_URL}`, headers: headerUpload }).post("/api/profile/upload", formData)
    },
}

const home = {
    component: async () => {
        return await httpService(false).get("/api/home/component")
    },
    page: async () => {
        return await httpService(false).get("/api/home/page")
    },
    newsletter: async (data) => {
        return await httpService(false).post("/api/home/newsletter", data)
    },
}

const store = {
    list: async (params) => {
        return await httpService(true).get(`/api/shop/list?${params}`)
    },
    filter: async () => {
        return await httpService(true).get("/api/shop/filter")
    },
}

const order = {
    list: async (params) => {
        return await httpService(true).get(`/api/order/list?${params}`)
    },
    billing: async (id) => {
        return await httpService(true).get(`/api/order/billing/${id}`)
    },
    product: async () => {
        return await httpService(true).get(`/api/order/product`)
    },
    cancel: async () => {
        return await httpService(true).get(`/api/order/cancel`)
    },
    cartDetail: async (id) => {
        return await httpService(true).get(`/api/order/cart/${id}`)
    },
    cartAdd: async (id, data) => {
        return await httpService(true).post(`/api/order/cart/${id}`, data)
    },
    cartDelete: async (id) => {
        return await httpService(true).delete(`/api/order/cart/${id}`)
    },
    wishlist: async (id) => {
        return await httpService(true).get(`/api/order/wishlist/${id}`)
    },
    detail: async (id) => {
        return await httpService(true).get(`/api/order/detail/${id}`)
    },
    listReview: async (id) => {
        return await httpService(true).get(`/api/order/review/${id}`)
    },
    createReview: async (id, data) => {
        return await httpService(true).post(`/api/order/review/${id}`, data)
    },
    checkout: async (id, data) => {
        return await httpService(true).post(`/api/order/checkout/${id}`, data)
    },
}

export default {
    ping,
    getFile,
    expiredMessage,
    auth,
    profile,
    store,
    home,
    order
}