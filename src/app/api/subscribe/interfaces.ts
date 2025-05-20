export interface EmailReputationResponse {
    email_address: string;
    email_deliverability: {
        status: 'deliverable' | 'undeliverable' | 'risky' | 'unknown';
        status_detail: string;
        is_format_valid: boolean;
        is_smtp_valid: boolean;
        is_mx_valid: boolean;
        mx_records: string[];
    };
    email_quality: {
        score: string;
        is_free_email: boolean;
        is_username_suspicious: boolean;
        is_disposable: boolean;
        is_catchall: boolean;
        is_subaddress: boolean;
        is_role: boolean;
        is_dmarc_enforced: boolean;
        is_spf_strict: boolean;
        minimum_age: number;
    };
    email_sender: {
        first_name: string | null;
        last_name: string | null;
        email_provider_name: string;
        organization_name: string;
        organization_type: string;
    };
    email_domain: {
        domain: string;
        domain_age: number;
        is_live_site: boolean;
        registrar: string;
        registrar_url: string;
        date_registered: string;
        date_last_renewed: string;
        date_expires: string;
        is_risky_tld: boolean;
    };
    email_risk: {
        address_risk_status: 'low' | 'medium' | 'high' | 'unknown';
        domain_risk_status: 'low' | 'medium' | 'high' | 'unknown';
    };
    email_breaches: {
        total_breaches: number;
        date_first_breached: string;
        date_last_breached: string;
        breached_domains: {
            domain: string;
            breach_date: string;
        }[];
    };
}
