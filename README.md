# Next.js Project with Tailwind CSS and React

This project is built using [Next.js](https://nextjs.org), [Tailwind CSS](https://tailwindcss.com), and React.

## Requirements

- **Node.js version:**
  ```
  v18.19.1
  ```

- **Next.js version:**
  ```
  v15.3.2
  ```

## Getting Started

### 1. Configure environment variables

Copy the example environment file and update the required keys:

```bash
cp env-example .env
```

Edit `.env` and fill in your credentials:

```env
NEXT_PUBLIC_GTM_ID=
NEXT_PUBLIC_ENVIRONMENT=development
CHECK_CRYPTO_API_KEY=
INTERCOM_SECRET_KEY=
```

### 2. Install dependencies

```bash
npm install
```

### 3. Run the development server

```bash
npm run dev
```

Once the server is running, open your browser at [http://localhost:3000](http://localhost:3000)

## Available Scripts

- `npm run dev` – Start the development server
- `npm run build` – Build for production
- `npm run start` – Start the production server
- `npm run lint` – Run ESLint to check code quality

## Technologies

- [Next.js](https://nextjs.org)
- [React](https://reactjs.org)
- [Tailwind CSS](https://tailwindcss.com)
- [TypeScript](https://www.typescriptlang.org)