"use client";

import React, { ChangeEvent, useEffect, useRef, useState } from "react";
import InputText from "@/components/InputText";
import clsx from "clsx";
import { Button } from "@/components/Button";
import { InputCheckbox } from "@/components/InputCheckbox";
import { IShowAlert } from "@/app/(backoffice)/refferals/page";
import { useLoading } from "@/context/LoadingContext";
import { dlPush } from "@/commons/utils";

const DefaultConsentMessage = () => (
  <>
    I consent to the use of my email address to
    receive<br className="hidden md:block" /> updates and launch announcements.
  </>
);

const DefaultCompactConsentMessage = () => (
  <>
    I consent to the use of my email address to receive news, updates, and important notifications.
  </>
);


function SubscribeForm({ cbShowAlert, compact = false, focusForced = true }: {
  cbShowAlert?: (payload: IShowAlert | null) => void,
  focusForced?: boolean,
  compact?: boolean
}) {
  const { setLoading, isLoading: sendingEmail } = useLoading();
  const [form, setForm] = useState<{ email: string, email_consent: boolean }>({
    email: "",
    email_consent: false
  });
  const inputEmail = useRef<HTMLInputElement | null>(null);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const canSubscribe = form.email_consent;

  useEffect(() => {
    if (focusForced) {
      inputEmail.current?.focus();
    }
  }, [inputEmail]);

  const validateEmail = () => {
    if (inputEmail.current) {
      const value = inputEmail.current.value.trim();
      inputEmail.current.value = value;

      if (value === "") {
        return false;
      }

      if (!value) {
        inputEmail.current.value = "";
        setErrorMessage("This field is required");
        return false;
      }

      if (!inputEmail.current.validity.valid) {
        setErrorMessage("Invalid email");
        return false;
      }

      setErrorMessage(null);
      return true;
    }
    return false;
  };

  const handlerSubmitForm = async (ev: React.ChangeEvent<HTMLFormElement>) => {
    ev.preventDefault();
    ev.stopPropagation();

    if (!validateEmail()) {
      return;
    }

    inputEmail.current?.blur();

    if (cbShowAlert) {
      cbShowAlert(null);
    }

    setLoading(true);

    try {
      const response = await fetch("/api/subscribe", {
        method: "POST",
        body: JSON.stringify({
          email: form.email
        })
      });

      const data: { success: boolean, message: string } = await response.json();

      if (!data.success) {
        if (data.message === "Email already exists") {
          dlPush({
            event: "subscribed_duplicate",
            form_id: "subscribe-form",
            source: "newsletter"
          });
        } else {
          dlPush({
            event: "subscribed_error",
            form_id: "subscribe-form",
            source: "newsletter",
            error_code: `HTTP_${response.status}`
          });
        }

        setErrorMessage(data.message);
        return false;
      }

      setForm({
        email: "",
        email_consent: false
      });


      dlPush({
        event: "subscribed_success",
        form_id: "subscribe-form",
        source: "newsletter",
        event_id: crypto.randomUUID()
      });

      if (cbShowAlert) {
        cbShowAlert({
          type: "success",
          message: "Congratulations! You have successfully subscribed."
        });
      }
    } catch (e) {
      console.info("error", e);

      dlPush({
        event: "subscribed_error",
        form_id: "subscribe-form",
        source: "newsletter",
        error_code: "NETWORK"
      });

      dlPush({
        event: "subscribed_error",
        form_id: "subscribe-form",
        source: "newsletter",
        error_code: "NETWORK"
      });

      return false;
    } finally {
      setLoading(false);

      setTimeout(() => {
        inputEmail.current?.focus();
      }, 100);
    }
  };

  return (
    <>
      <form noValidate={true} onSubmit={handlerSubmitForm}
            className={clsx("grid grid-rows-2 items-start gap-2", [compact ? "mb-4 lg:flex" : "md:flex my-4"])}>
        <InputText
          ref={inputEmail}
          required={true}
          type={"email"}
          disabled={sendingEmail}
          onBlur={validateEmail}
          className={clsx(!!errorMessage ? "placeholder:text-rose-500" : null)}
          onChange={(e: ChangeEvent<HTMLInputElement>) => {
            const value = e.target.value;
            setForm(item => ({
              ...item,
              "email": value
            }));
          }}
          value={form.email}
          errorMessage={errorMessage}
          placeholder={"Enter your email"}
          name={"email"} />

        <Button disabled={!canSubscribe || sendingEmail || !!errorMessage} type={"submit"}
                className="w-full md:w-auto">
          SUBSCRIBE
        </Button>
      </form>

      <div>
        <InputCheckbox
          checked={form.email_consent}
          onChange={(e) => {
            const checked = e.target.checked;
            setForm(item => ({
              ...item,
              "email_consent": checked
            }));
          }}
          name="email_consent">
          <p className="select-none w-full text-white text-base font-medium leading-normal">
            {!compact && <DefaultConsentMessage />}
            {compact && <DefaultCompactConsentMessage />}
          </p>
        </InputCheckbox>
      </div>
    </>
  );
}

export default SubscribeForm;