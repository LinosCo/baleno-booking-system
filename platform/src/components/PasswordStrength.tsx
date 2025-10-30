import { evaluatePassword, strengthScore } from '@/utils/passwordStrength';
import { Progress } from '@/components/ui/progress';

interface PasswordStrengthProps {
  password: string;
}

export const PasswordStrength: React.FC<PasswordStrengthProps> = ({ password }) => {
  const checks = evaluatePassword(password);
  const score = strengthScore(password);

  const getLabel = () => {
    if (!password) return 'Enter a password to see its strength';
    if (score < 50) return 'Weak';
    if (score < 75) return 'Moderate';
    if (score < 100) return 'Strong';
    return 'Excellent';
  };

  return (
    <div className="space-y-3">
      <div className="flex items-center justify-between text-xs font-semibold text-slate-500">
        <span>Password strength</span>
        <span className="text-brand-600">{getLabel()}</span>
      </div>
      <Progress value={score} />
      <ul className="grid gap-1 text-xs text-slate-500 md:grid-cols-2">
        {checks.map((check) => (
          <li key={check.label} className={check.passed ? 'text-brand-600 font-medium' : ''}>
            {check.passed ? '✓' : '○'} {check.label}
          </li>
        ))}
      </ul>
    </div>
  );
};
