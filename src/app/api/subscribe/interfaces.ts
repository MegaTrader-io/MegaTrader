export interface EmailValidationResponse {
    email: string;
    autocorrect: string;
    deliverability: 'DELIVERABLE' | 'UNDELIVERABLE' | 'RISKY' | string;
    quality_score: string;
    is_valid_format: {
        value: boolean;
        text: 'TRUE' | 'FALSE';
    };
    is_free_email: {
        value: boolean;
        text: 'TRUE' | 'FALSE';
    };
    is_disposable_email: {
        value: boolean;
        text: 'TRUE' | 'FALSE';
    };
    is_role_email: {
        value: boolean;
        text: 'TRUE' | 'FALSE';
    };
    is_catchall_email: {
        value: boolean | null;
        text: 'TRUE' | 'FALSE' | 'UNKNOWN';
    };
    is_mx_found: {
        value: boolean;
        text: 'TRUE' | 'FALSE';
    };
    is_smtp_valid: {
        value: boolean;
        text: 'TRUE' | 'FALSE';
    };
}

export interface KlaviyoLocation {
    ip: string;
    city: string;
    region: string;
    zip?: string;
    country: string;
    longitude: number;
    latitude: number;
    timezone: string;
}


export interface IpGeolocationResponse {
    ip_address: string;
    city: string;
    city_geoname_id: number;
    region: string;
    region_iso_code: string;
    region_geoname_id: number;
    postal_code: string | null;
    country: string;
    country_code: string;
    country_geoname_id: number;
    country_is_eu: boolean;
    continent: string;
    continent_code: string;
    continent_geoname_id: number;
    longitude: number;
    latitude: number;
    security: {
        is_vpn: boolean;
    };
    timezone: {
        name: string;
        abbreviation: string;
        gmt_offset: number;
        current_time: string;
        is_dst: boolean;
    };
    flag: {
        emoji: string;
        unicode: string;
        png: string;
        svg: string;
    };
    currency: {
        currency_name: string;
        currency_code: string;
    };
    connection: {
        autonomous_system_number: number;
        autonomous_system_organization: string;
        connection_type: string | null;
        isp_name: string | null;
        organization_name: string | null;
    };
}
