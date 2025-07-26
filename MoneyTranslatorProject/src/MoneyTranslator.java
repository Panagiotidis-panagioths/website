import java.awt.*;
import java.awt.event.*;
import javax.swing.*;
import javax.swing.border.EmptyBorder;

public class MoneyTranslator {
    private static JLabel amountLabel, resultLabel;
    private static JTextField amountField;
    private static JComboBox<String> fromCurrency, toCurrency;
    private static JButton convertButton;

    
    private static String[] currencies = {
        "USD (US Dollar)", "EUR (Euro)", "GBP (British Pound)", "JPY (Japanese Yen)"
    };

    // 4x4 exchange rate matrix (based on sample rates)
    private static double[][] exchangeRates = {
        {1.0, 0.85, 0.75, 110.0},    // USD
        {1.18, 1.0, 0.88, 130.0},    // EUR
        {1.33, 1.14, 1.0, 146.0},    // GBP
        {0.0091, 0.0077, 0.0068, 1.0} // JPY
    };

    public static void main(String[] args) {
        JFrame frame = new JFrame("Money Translator");
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        frame.setMinimumSize(new Dimension(400, 250));
        frame.setLayout(new GridBagLayout());
        GridBagConstraints gbc = new GridBagConstraints();
        gbc.insets = new Insets(10, 10, 10, 10);
        gbc.fill = GridBagConstraints.HORIZONTAL;

        amountLabel = new JLabel("Enter Amount:");
        amountLabel.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        gbc.gridx = 0;
        gbc.gridy = 0;
        frame.add(amountLabel, gbc);

        amountField = new JTextField(10);
        amountField.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        amountField.setBorder(BorderFactory.createLineBorder(Color.GRAY));
        gbc.gridx = 1;
        gbc.gridy = 0;
        frame.add(amountField, gbc);

        fromCurrency = new JComboBox<>(currencies);
        fromCurrency.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        fromCurrency.setBorder(BorderFactory.createLineBorder(Color.GRAY));
        gbc.gridx = 0;
        gbc.gridy = 1;
        frame.add(new JLabel("From Currency:"), gbc);
        gbc.gridx = 1;
        gbc.gridy = 1;
        frame.add(fromCurrency, gbc);

        toCurrency = new JComboBox<>(currencies);
        toCurrency.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        toCurrency.setBorder(BorderFactory.createLineBorder(Color.GRAY));
        gbc.gridx = 0;
        gbc.gridy = 2;
        frame.add(new JLabel("To Currency:"), gbc);
        gbc.gridx = 1;
        gbc.gridy = 2;
        frame.add(toCurrency, gbc);

        convertButton = new JButton("Convert");
        convertButton.setFont(new Font("Segoe UI", Font.BOLD, 14));
        convertButton.setBackground(new Color(0, 120, 215));
        convertButton.setForeground(Color.WHITE);
        convertButton.setFocusPainted(false);
        convertButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent e) {
                convertCurrency();
            }
        });
        gbc.gridx = 1;
        gbc.gridy = 3;
        gbc.fill = GridBagConstraints.NONE;
        gbc.anchor = GridBagConstraints.CENTER;
        frame.add(convertButton, gbc);

        resultLabel = new JLabel("Result: ");
        resultLabel.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        resultLabel.setBorder(BorderFactory.createEmptyBorder(5, 0, 0, 0));
        gbc.gridx = 0;
        gbc.gridy = 4;
        gbc.gridwidth = 2;
        gbc.fill = GridBagConstraints.HORIZONTAL;
        frame.add(resultLabel, gbc);

        JPanel panel = new JPanel();
        panel.setBorder(new EmptyBorder(10, 10, 10, 10));
        panel.add(frame.getContentPane());
        frame.setContentPane(panel);

        frame.setLocationRelativeTo(null);
        frame.setVisible(true);
    }

    private static void convertCurrency() {
        try {
            double amount = Double.parseDouble(amountField.getText());
            int fromIndex = fromCurrency.getSelectedIndex();
            int toIndex = toCurrency.getSelectedIndex();

            if (fromIndex == toIndex) {
                resultLabel.setText("Result: " + String.format("%.2f", amount) + " " + currencies[fromIndex].split(" ")[0]);
                return;
            }

            double convertedAmount = amount * exchangeRates[fromIndex][toIndex];
            resultLabel.setText("Result: " + String.format("%.2f", convertedAmount) + " " + currencies[toIndex].split(" ")[0]);
        } catch (NumberFormatException e) {
            resultLabel.setText("Result: Please enter a valid number");
        }
    }
}
