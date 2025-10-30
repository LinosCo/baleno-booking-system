export type StrengthCheck = {
  label: string;
  passed: boolean;
};

export const evaluatePassword = (password: string): StrengthCheck[] => {
  const checks: StrengthCheck[] = [
    { label: 'At least 8 characters', passed: password.length >= 8 },
    { label: 'Contains uppercase and lowercase', passed: /[a-z]/.test(password) && /[A-Z]/.test(password) },
    { label: 'Contains a number', passed: /\d/.test(password) },
    { label: 'Contains a special character', passed: /[^A-Za-z0-9]/.test(password) },
  ];
  return checks;
};

export const strengthScore = (password: string) =>
  evaluatePassword(password).reduce((score, check) => score + (check.passed ? 1 : 0), 0) * 25;
